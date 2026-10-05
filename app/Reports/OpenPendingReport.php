<?php

namespace App\Reports;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderShipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Foto de lo que sigue abierto hoy. Subvistas:
 *  - 'ship'      : embarques activos con ETA vencido.
 *  - 'customs'   : aduanas activas, por antigüedad (no tienen ETA).
 *  - 'warehouse' : almacenes activos, por antigüedad (no tienen ETA).
 *  - 'order'     : órdenes activas, por antigüedad.
 */
class OpenPendingReport extends Report
{
    public const SHIP = 'ship';
    public const CUSTOMS = 'customs';
    public const WAREHOUSE = 'warehouse';
    public const ORDER = 'order';

    public readonly Carbon $today;

    /** Tipo de servicio efectivo (el elegido, o el de la clase elegida). Null = todos. */
    public readonly ?int $typeId;

    private ?Collection $raw = null;

    public function __construct(
        public readonly string $view = self::SHIP,
        public readonly ?int $clientId = null,
        ?int $serviceTypeId = null,
        public readonly ?int $serviceClassId = null,
        array $table = [],
    ) {
        $this->today = Carbon::today();
        $this->typeId = ServiceKind::resolveTypeId($serviceTypeId, $serviceClassId);
        $this->table = $table;
    }

    /**
     * Subvistas que tienen sentido con el filtro de servicio: si se eligió un tipo
     * (o una clase, que pertenece a un solo tipo) solo quedan ese tipo y las órdenes.
     *
     * @return array<string, string> clave => etiqueta del botón
     */
    public static function views(?int $typeId = null): array
    {
        $views = [
            self::SHIP => 'Embarques con ETA vencido',
            self::CUSTOMS => 'Aduanas abiertas',
            self::WAREHOUSE => 'Almacenes abiertos',
            self::ORDER => 'Órdenes activas por antigüedad',
        ];

        if ($kind = ServiceKind::byType($typeId)) {
            return array_intersect_key($views, [$kind->key => 1, self::ORDER => 1]);
        }

        return $views;
    }

    public function title(): string
    {
        return self::views()[$this->view] ?? 'Pendientes abiertos';
    }

    public function isShip(): bool
    {
        return $this->view === self::SHIP;
    }

    /** Aduana o Almacén: servicios sin ETA, medidos por días abiertos. */
    public function isService(): bool
    {
        return in_array($this->view, [self::CUSTOMS, self::WAREHOUSE], true);
    }

    private function kind(): ?ServiceKind
    {
        return ServiceKind::all()[$this->view] ?? null;
    }

    private function scopeOrder(Builder $query): Builder
    {
        return $query->when($this->clientId, fn ($q, $id) => $q->where('client_id', $id));
    }

    /** Servicios activos (no cerrados ni cancelados) de un tipo, con los filtros aplicados. */
    private function services(ServiceKind $kind): Builder
    {
        return $kind->model::query()
            ->whereNotIn($kind->statusColumn, $kind->closedStatuses())
            ->whereHas('order', fn ($q) => $this->scopeOrder($q))
            ->when($this->serviceClassId, fn ($q, $id) => $q->where('service_class_id', $id));
    }

    private function shipments(): Builder
    {
        return $this->services(ServiceKind::all()[self::SHIP]);
    }

    private function orders(): Builder
    {
        return $this->scopeOrder(Order::query())
            ->where('order_status_id', OrderStatusEnum::ACTIVE)
            ->when($this->typeId, function ($q, $typeId) {
                $relation = match ($typeId) {
                    1 => 'shipments',
                    2 => 'imports',
                    3 => 'storages',
                };

                return $q->whereHas($relation, fn ($s) => $s
                    ->when($this->serviceClassId, fn ($c, $id) => $c->where('service_class_id', $id)));
            });
    }

    /** Conteos de los botones "Mostrar:" (siempre todos los visibles, sin importar la subvista activa). */
    public function counts(): array
    {
        $counts = [];
        foreach (array_keys(self::views($this->typeId)) as $key) {
            $counts[$key] = match ($key) {
                self::SHIP => $this->shipments()->whereDate('estimated_time_arrival', '<', $this->today)->count(),
                self::ORDER => $this->orders()->count(),
                default => $this->services(ServiceKind::all()[$key])->count(),
            };
        }

        return $counts;
    }

    /** Filas de la subvista activa, de la más antigua a la más reciente (antes de ordenar/filtrar la tabla). */
    protected function rawRows(): Collection
    {
        return $this->raw ??= match (true) {
            $this->isShip() => $this->shipRows(),
            $this->isService() => $this->serviceRows(),
            default => $this->orderRows(),
        };
    }

    protected function defaultSort(): array
    {
        // Equivale a "del más antiguo al más reciente"
        return ['days', 'desc'];
    }

    protected function sortKeys(): array
    {
        return match (true) {
            $this->isShip() => [
                'code' => fn ($r) => self::normalize((string) $r->code),
                'client' => fn ($r) => self::normalize($r->client),
                'route' => fn ($r) => self::normalize($r->route),
                'date' => fn ($r) => $r->date->timestamp,
                'days' => fn ($r) => $r->days,
                'status' => fn ($r) => $r->status === null ? null : self::normalize($r->status),
            ],
            $this->isService() => [
                'code' => fn ($r) => self::normalize((string) $r->code),
                'client' => fn ($r) => self::normalize($r->client),
                'reference' => fn ($r) => self::normalize((string) $r->reference),
                'date' => fn ($r) => $r->date->timestamp,
                'days' => fn ($r) => $r->days,
                'status' => fn ($r) => self::normalize($r->status->label()),
            ],
            default => [
                'code' => fn ($r) => self::normalize((string) $r->code),
                'client' => fn ($r) => self::normalize($r->client),
                'services' => fn ($r) => $r->services,
                'date' => fn ($r) => $r->date->timestamp,
                'days' => fn ($r) => $r->days,
                'status' => fn ($r) => self::normalize($r->status->label()),
            ],
        };
    }

    protected function searchText(object $row): string
    {
        return match (true) {
            $this->isShip() => implode(' ', [$row->code, $row->order_code, $row->client, $row->route, $row->status ?? 'No actualizado']),
            $this->isService() => implode(' ', [$row->code, $row->order_code, $row->client, $row->reference, $row->status->label()]),
            default => implode(' ', [$row->code, $row->client]),
        };
    }

    protected function columnFilterKeys(): array
    {
        return ['min_days', 'status'];
    }

    protected function passesFilters(object $row): bool
    {
        if (($min = $this->tableOption('min_days')) !== null && $row->days <= (int) $min) {
            return false;
        }

        if (($status = $this->tableOption('status')) !== null) {
            $value = match (true) {
                $this->isShip() => $row->status ?? self::NO_STATUS,
                $this->isService() => (string) $row->status->value,
                default => null,
            };

            if ($value !== null && $value !== $status) {
                return false;
            }
        }

        return true;
    }

    /** Valor del filtro "Estatus" para embarques que todavía no tienen ningún estatus de rastreo. */
    public const NO_STATUS = '__none';

    /** Opciones del filtro "Estatus", según los estatus que de verdad aparecen en la subvista. */
    public function statusOptions(): array
    {
        $raw = $this->rawRows();

        if ($this->isShip()) {
            $options = $raw->pluck('status')->filter()->unique()->sort()->mapWithKeys(fn ($name) => [$name => $name])->all();

            return $raw->contains(fn ($r) => $r->status === null)
                ? $options + [self::NO_STATUS => 'No actualizado']
                : $options;
        }

        if ($this->isService()) {
            return $raw->pluck('status')->unique(fn ($s) => $s->value)
                ->mapWithKeys(fn ($s) => [(string) $s->value => $s->label()])->all();
        }

        return []; // Órdenes: todas son "Activo", el filtro no aporta
    }

    private function shipRows(): Collection
    {
        return $this->shipments()
            ->whereDate('estimated_time_arrival', '<', $this->today)
            ->with(['order.client', 'originCity', 'originState', 'destinationCity', 'destinationState', 'latestLocation.status'])
            ->orderBy('estimated_time_arrival')
            ->orderBy('id')
            ->get()
            ->map(fn (OrderShipment $s) => (object) [
                'order_id' => $s->order_id,
                'id' => $s->id,
                'code' => $s->tracking_code,
                'order_code' => $s->order->code,
                'client' => self::clientLabel($s->order->client),
                'image' => $s->order->client?->image,
                'route' => trim(($s->originCity?->name ?? '—').' → '.($s->destinationCity?->name ?? '—')),
                'date' => Carbon::parse($s->estimated_time_arrival),
                'days' => (int) Carbon::parse($s->estimated_time_arrival)->startOfDay()->diffInDays($this->today),
                'status' => $s->latestLocation?->status?->name,
            ]);
    }

    private function serviceRows(): Collection
    {
        $kind = $this->kind();

        return $this->services($kind)
            ->with('order.client')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn ($s) => (object) [
                'order_id' => $s->order_id,
                'id' => $s->id,
                'code' => $s->tracking_code,
                'order_code' => $s->order->code,
                'reference' => $s->reference,
                'client' => self::clientLabel($s->order->client),
                'image' => $s->order->client?->image,
                'date' => $s->created_at,
                'days' => (int) $s->created_at->copy()->startOfDay()->diffInDays($this->today),
                'status' => $s->{$kind->statusColumn},
                'url' => route($kind->showRoute, ['order' => $s->order_id, $kind->showParam => $s->id]),
            ]);
    }

    private function orderRows(): Collection
    {
        return $this->orders()
            ->with('client')
            ->withCount(['shipments', 'imports', 'storages'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Order $o) => (object) [
                'id' => $o->id,
                'code' => $o->code,
                'client' => self::clientLabel($o->client),
                'image' => $o->client?->image,
                'services' => $o->shipments_count + $o->imports_count + $o->storages_count,
                'date' => $o->created_at,
                'days' => (int) $o->created_at->copy()->startOfDay()->diffInDays($this->today),
                'status' => OrderStatusEnum::ACTIVE,
            ]);
    }

    /**
     * Rangos de antigüedad para la barra apilada: [etiqueta, mínimo, máximo (null = sin tope), color].
     * Los colores son clases Tailwind completas (deben aparecer literales para compilar).
     */
    public function buckets(): array
    {
        return $this->isShip()
            ? [
                ['1 a 7 días', 1, 7, 'bg-gray-400 dark:bg-gray-500'],
                ['8 a 30 días', 8, 30, 'bg-amber-500'],
                ['Más de 30 días', 31, null, 'bg-lits-red-500'],
            ]
            : [
                ['0 a 7 días', 0, 7, 'bg-green-500'],
                ['8 a 30 días', 8, 30, 'bg-gray-400 dark:bg-gray-500'],
                ['31 a 90 días', 31, 90, 'bg-amber-500'],
                ['Más de 90 días', 91, null, 'bg-lits-red-500'],
            ];
    }

    /** Distribución por antigüedad: lista de [etiqueta, total, color]. */
    public function distribution(): array
    {
        $rows = $this->rows();

        return array_map(fn ($b) => [
            $b[0],
            $rows->filter(fn ($r) => $r->days >= $b[1] && ($b[2] === null || $r->days <= $b[2]))->count(),
            $b[3],
        ], $this->buckets());
    }

    /** Franja de totales: lista de [cifra, etiqueta, ¿resaltar en rojo?]. */
    public function summary(): array
    {
        $rows = $this->rows();
        $over30 = $rows->where('days', '>', 30)->count();

        if ($this->isShip()) {
            return [
                [$this->shipments()->count(), 'Embarques activos', false],
                [$rows->count(), 'Con ETA vencido', true],
                [$over30, 'Vencidos hace más de 30 días', false],
                [$rows->where('days', '<=', 7)->count(), 'Vencidos hace 1 a 7 días', false],
            ];
        }

        return [
            [$rows->count(), $this->isService() ? $this->kind()->openLabel : 'Órdenes activas', false],
            [$over30, 'Abiertas hace más de 30 días', true],
            [$rows->where('days', '>', 90)->count(), 'Abiertas hace más de 90 días', false],
            [$rows->where('days', '<=', 30)->count(), 'Abiertas hace 30 días o menos', false],
        ];
    }

    /** Nota explicativa debajo de la barra de antigüedad (HTML con <b>, sin datos del usuario). */
    public function insight(): ?string
    {
        $total = $this->rows()->count();
        $over30 = $this->rows()->where('days', '>', 30)->count();

        if ($total === 0) {
            return null;
        }

        return match (true) {
            $this->isShip() => "<b>{$over30} de los {$total}</b> llevan más de 30 días vencidos. Es probable que ya se hayan entregado y nunca se cerraron en el sistema.",
            $this->isService() => "<b>{$over30} de los {$total}</b> llevan más de 30 días abiertos. Es probable que el servicio ya haya concluido y nunca se cerró en el sistema.",
            default => "<b>{$over30} de las {$total}</b> llevan más de 30 días abiertas. El ciclo normal de una orden es de unos pocos días; una orden abierta tanto tiempo casi siempre quedó sin cerrar.",
        };
    }

    public function headings(): array
    {
        return match (true) {
            $this->isShip() => ['Embarque', 'Orden', 'Cliente', 'Ruta', 'ETA', 'Días vencido', 'Último estatus'],
            $this->isService() => [$this->kind()->label, 'Orden', 'Cliente', 'Referencia', 'Creado', 'Días abierto', 'Estatus'],
            default => ['Orden', 'Cliente', 'Servicios', 'Creada', 'Días abierta', 'Estatus'],
        };
    }

    public function exportRows(): array
    {
        return $this->rows()->map(fn ($r) => match (true) {
            $this->isShip() => [$r->code, $r->order_code, $r->client, $r->route, $r->date->format('d/m/Y'), $r->days, $r->status ?? 'No actualizado'],
            $this->isService() => [$r->code, $r->order_code, $r->client, $r->reference, $r->date->format('d/m/Y'), $r->days, $r->status->label()],
            default => [$r->code, $r->client, $r->services, $r->date->format('d/m/Y'), $r->days, $r->status->label()],
        })->all();
    }
}
