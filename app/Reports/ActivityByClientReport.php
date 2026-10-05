<?php

namespace App\Reports;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityByClientReport extends Report
{
    private ?Collection $raw = null;

    /** Tipo de servicio efectivo (el elegido, o el de la clase elegida). Null = todos. */
    public readonly ?int $typeId;

    public function __construct(
        public readonly Carbon $from,
        public readonly Carbon $to,
        public readonly ?int $clientId = null,
        ?int $serviceTypeId = null,
        public readonly ?int $serviceClassId = null,
        array $table = [],
    ) {
        $this->typeId = ServiceKind::resolveTypeId($serviceTypeId, $serviceClassId);
        $this->table = $table;
    }

    public function title(): string
    {
        return 'Actividad por cliente';
    }

    /** Tipos de servicio que entran al reporte según el filtro. @return array<string, ServiceKind> */
    public function kinds(): array
    {
        return $this->typeId
            ? array_filter(ServiceKind::all(), fn (ServiceKind $k) => $k->typeId === $this->typeId)
            : ServiceKind::all();
    }

    /** Servicios de un tipo dentro de las órdenes del periodo, con el filtro de clase si aplica. */
    private function services(ServiceKind $kind, bool $onlyShownClients = false): Builder
    {
        return DB::table($kind->table.' as s')
            ->whereNull('s.deleted_at')
            ->whereIn('s.order_id', (clone $this->orders($onlyShownClients))->select('o.id'))
            ->when($this->serviceClassId, fn ($q, $id) => $q->where('s.service_class_id', $id));
    }

    /**
     * Órdenes del periodo, con los filtros aplicados. Con $onlyShownClients se limita
     * además a los clientes que dejó la búsqueda/filtros de la tabla.
     */
    private function orders(bool $onlyShownClients = false): Builder
    {
        return DB::table('orders as o')
            ->whereNull('o.deleted_at')
            ->whereBetween('o.created_at', [$this->from->copy()->startOfDay(), $this->to->copy()->endOfDay()])
            ->when($this->clientId, fn ($q, $id) => $q->where('o.client_id', $id))
            ->when($onlyShownClients, fn ($q) => $q->whereIn('o.client_id', $this->rows()->pluck('client_id')->all()))
            ->when($this->typeId, function ($q, $typeId) {
                $kind = ServiceKind::byType($typeId);

                // Solo órdenes que tienen al menos un servicio de ese tipo (y de esa clase, si se eligió)
                return $q->whereExists(fn ($sub) => $sub
                    ->selectRaw('1')
                    ->from($kind->table.' as f')
                    ->whereColumn('f.order_id', 'o.id')
                    ->whereNull('f.deleted_at')
                    ->when($this->serviceClassId, fn ($s, $id) => $s->where('f.service_class_id', $id)));
            });
    }

    /** Una fila por cliente, de mayor a menor número de órdenes (antes de ordenar/filtrar la tabla). */
    protected function rawRows(): Collection
    {
        if ($this->raw) {
            return $this->raw;
        }

        $byKind = [];
        foreach ($this->kinds() as $kind) {
            $byKind[$kind->key] = $this->services($kind)
                ->join('orders as o', 'o.id', '=', 's.order_id')
                ->groupBy('o.client_id')
                ->selectRaw('o.client_id, COUNT(*) as total')
                ->pluck('total', 'client_id');
        }

        return $this->raw = $this->orders()
            ->join('clients as c', 'c.id', '=', 'o.client_id')
            ->selectRaw('o.client_id, c.image,
                CONCAT(COALESCE(c.company_name, ""), " / ", COALESCE(c.trade_name, "")) as client,
                COUNT(*) as orders,
                SUM(o.order_status_id = ?) as active,
                SUM(o.order_status_id = ?) as closed,
                SUM(CASE WHEN o.order_status_id = ? AND o.closed_at IS NOT NULL
                    THEN DATEDIFF(o.closed_at, DATE(o.created_at)) ELSE 0 END) as days_sum,
                SUM(CASE WHEN o.order_status_id = ? AND o.closed_at IS NOT NULL THEN 1 ELSE 0 END) as days_n,
                MAX(o.created_at) as last_order', [
                OrderStatusEnum::ACTIVE->value,
                OrderStatusEnum::CLOSED->value,
                OrderStatusEnum::CLOSED->value,
                OrderStatusEnum::CLOSED->value,
            ])
            ->groupBy('o.client_id', 'c.image', 'c.trade_name', 'c.company_name')
            ->orderByDesc('orders')
            ->orderBy('client')
            ->get()
            ->map(fn ($r) => (object) [
                'client_id' => (int) $r->client_id,
                'image' => $r->image,
                'client' => $r->client,
                'orders' => (int) $r->orders,
                'active' => (int) $r->active,
                'closed' => (int) $r->closed,
                // Suma y conteo de las órdenes cerradas: permiten promediar de forma ponderada al filtrar
                'days_sum' => (int) $r->days_sum,
                'days_n' => (int) $r->days_n,
                'days' => $r->days_n > 0 ? round($r->days_sum / $r->days_n, 1) : null,
                'last_order' => $r->last_order ? Carbon::parse($r->last_order) : null,
                // Servicios por tipo; los tipos que el filtro deja fuera quedan en 0
                'services' => collect(ServiceKind::all())->map(
                    fn (ServiceKind $k) => (int) ($byKind[$k->key][$r->client_id] ?? 0)
                )->all(),
            ]);
    }

    protected function defaultSort(): array
    {
        return ['orders', 'desc'];
    }

    protected function sortKeys(): array
    {
        return [
            'client' => fn ($r) => self::normalize($r->client),
            'orders' => fn ($r) => $r->orders,
            'active' => fn ($r) => $r->active,
            'closed' => fn ($r) => $r->closed,
            'days' => fn ($r) => $r->days,
            'ship' => fn ($r) => $r->services['ship'],
            'customs' => fn ($r) => $r->services['customs'],
            'warehouse' => fn ($r) => $r->services['warehouse'],
            'last_order' => fn ($r) => $r->last_order?->timestamp,
        ];
    }

    protected function searchText(object $row): string
    {
        return $row->client;
    }

    protected function columnFilterKeys(): array
    {
        return ['min_orders', 'only_active', 'min_close_days'];
    }

    protected function passesFilters(object $row): bool
    {
        if (($min = $this->tableOption('min_orders')) !== null && $row->orders < (int) $min) {
            return false;
        }

        if ($this->tableOption('only_active') !== null && $row->active < 1) {
            return false;
        }

        if (($min = $this->tableOption('min_close_days')) !== null && ($row->days === null || $row->days <= (float) $min)) {
            return false;
        }

        return true;
    }

    /**
     * Totales de lo que se ve en la tabla (ya buscada/filtrada). El promedio de días se pondera
     * por órdenes cerradas: no es el promedio de los promedios de cada cliente.
     */
    public function totals(): array
    {
        $rows = $this->rows();
        $daysN = $rows->sum('days_n');

        $services = collect(ServiceKind::all())->map(
            fn (ServiceKind $k) => (int) $rows->sum(fn ($r) => $r->services[$k->key])
        )->all();

        return [
            'orders' => $rows->sum('orders'),
            'clients' => $rows->count(),
            'active' => $rows->sum('active'),
            'closed' => $rows->sum('closed'),
            'days' => $daysN > 0 ? round($rows->sum('days_sum') / $daysN, 1) : null,
            'services' => $services,
            'services_total' => array_sum($services),
        ];
    }

    /**
     * Servicios del periodo por tipo (Embarque, Aduana, Almacén) con sus clases desglosadas,
     * de los clientes que quedan en la tabla. Solo entran los tipos con al menos un servicio.
     *
     * @return Collection<int, object{label: string, total: int, classes: Collection}>
     */
    public function mix(): Collection
    {
        $onlyShown = $this->hasTableFilters();

        return collect($this->kinds())->map(function (ServiceKind $kind) use ($onlyShown) {
            $classes = $this->services($kind, $onlyShown)
                ->leftJoin('service_classes as sc', 'sc.id', '=', 's.service_class_id')
                ->selectRaw('COALESCE(sc.name, "Sin clase") as label, COUNT(*) as total')
                ->groupBy('sc.id', 'sc.name')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($r) => (object) ['label' => $r->label, 'total' => (int) $r->total]);

            return (object) ['label' => $kind->plural, 'total' => $classes->sum('total'), 'classes' => $classes];
        })->filter(fn ($k) => $k->total > 0)->values();
    }

    public function headings(): array
    {
        return ['Cliente', 'Órdenes', 'Activas', 'Cerradas', 'Días para cerrar', 'Embarques', 'Aduanas', 'Almacenes', 'Última orden'];
    }

    public function exportRows(): array
    {
        $totals = $this->totals();

        $rows = $this->rows()->map(fn ($r) => [
            $r->client,
            $r->orders,
            $r->active,
            $r->closed,
            $r->days,
            $r->services['ship'],
            $r->services['customs'],
            $r->services['warehouse'],
            $r->last_order?->format('d/m/Y'),
        ])->all();

        $rows[] = [
            'Total ('.$totals['clients'].' clientes)', $totals['orders'], $totals['active'], $totals['closed'], $totals['days'],
            $totals['services']['ship'], $totals['services']['customs'], $totals['services']['warehouse'], null,
        ];

        return $rows;
    }
}
