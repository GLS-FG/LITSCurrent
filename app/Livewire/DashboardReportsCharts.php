<?php

namespace App\Livewire;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\OrderShipment;
use App\Models\WarehouseStorage;
use App\View\Helpers\DashboardChartData;
use App\View\Helpers\DashboardDataset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardReportsCharts extends Component
{
    public string $period = 'month';

    public function setPeriod(string $period): void
    {
        if (! in_array($period, ['month', 'semester', 'all-time'], true)) {
            return;
        }

        $this->period = $period;

        // DashboardChartData/DashboardDataset are plain objects Livewire can't
        // synthesize over the wire, so the event payload is sent as plain arrays.
        $this->dispatch(
            'reports-charts-updated',
            orders: json_decode(json_encode($this->buildOrdersChartData()), true),
            doughnuts: json_decode(json_encode($this->buildDoughnutCharts()), true),
        );
    }

    public function render()
    {
        return view('livewire.dashboard-reports-charts', [
            'ordersChartData' => $this->buildOrdersChartData(),
            'doughnutCharts' => $this->buildDoughnutCharts(),
        ]);
    }

    /**
     * The lower bound used to scope the "Closed orders" doughnut charts to the
     * same window covered by the selected period's line chart.
     */
    private function sinceDate(): Carbon
    {
        return match ($this->period) {
            'semester' => Carbon::now()->subMonths(6),
            'all-time' => Carbon::now()->subYears(Carbon::now()->year - 2018),
            default => Carbon::now()->subWeeks(4),
        };
    }

    private function buildOrdersChartData(): DashboardChartData
    {
        $user = Auth::user();

        $labels = [];
        $datasets = [];
        $datasets[] = new DashboardDataset(__('Orders'), [], '#4338CA', '#4338CA');
        $datasets[] = new DashboardDataset(__('Shipments'), [], '#0891B2', '#0891B2');
        $datasets[] = new DashboardDataset(__('Customs'), [], '#7C3AED', '#7C3AED');
        $datasets[] = new DashboardDataset(__('Warehouses'), [], '#B45309', '#B45309');

        $currentDate = Carbon::now();
        $limit = 4;
        $dateLabelFormat = '[' . __('Week') . '] W';
        if ($this->period === 'semester') {
            $limit = 6;
            $dateLabelFormat = 'MMMM';
        } elseif ($this->period === 'all-time') {
            $limit = $currentDate->year - 2018;
            $dateLabelFormat = 'Y';
        }

        for ($i = 0; $i < $limit; $i++) {
            $dateLabel = ucfirst($currentDate->isoFormat($dateLabelFormat));
            $dateStart = $currentDate->copy();
            $dateEnd = $currentDate->copy();
            if ($this->period === 'semester') {
                $dateStart->startOfMonth();
                $dateEnd->endOfMonth();
            } elseif ($this->period === 'all-time') {
                $dateStart->startOfYear();
                $dateEnd->endOfYear();
            } else {
                $dateStart->startOfWeek();
                $dateEnd->endOfWeek();
            }

            array_unshift($labels, $dateLabel);

            $ordersCount = Order::where('order_status_id', '<>', OrderStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id))
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])
                ->count();
            $shipmentsCount = OrderShipment::where('order_shipment_status_id', '<>', OrderShipmentStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', fn ($query) => $query
                        ->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id)))
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])
                ->count();
            $importsCount = OrderImport::where('order_import_status_id', '<>', OrderImportStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', fn ($query) => $query
                        ->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id)))
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])
                ->count();
            $warehouseCount = WarehouseStorage::where('warehouse_storage_status_id', '<>', WarehouseStorageStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', fn ($query) => $query
                        ->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id)))
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])
                ->count();

            array_unshift($datasets[0]->data, $ordersCount);
            array_unshift($datasets[1]->data, $shipmentsCount);
            array_unshift($datasets[2]->data, $importsCount);
            array_unshift($datasets[3]->data, $warehouseCount);

            if ($this->period === 'semester') {
                $currentDate->subMonth();
            } elseif ($this->period === 'all-time') {
                $currentDate->subYear();
            } else {
                $currentDate->subWeek();
            }
        }

        return new DashboardChartData($labels, $datasets);
    }

    /**
     * @return array<string, DashboardChartData>
     */
    private function buildDoughnutCharts(): array
    {
        $user = Auth::user();
        $since = $this->sinceDate()->toDateTimeString();
        $labels = [__('Active'), __('Closed')];
        $colors = ['#D97706', '#16A34A'];

        $ordersActive = Order::whereNotIn('order_status_id', [OrderStatusEnum::CANCELED, OrderStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id))
            ->where('created_at', '>=', $since)->count();
        $ordersClosed = Order::where('order_status_id', OrderStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id))
            ->where('created_at', '>=', $since)->count();

        $shipmentsActive = OrderShipment::whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CANCELED, OrderShipmentStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();
        $shipmentsClosed = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();

        $importsActive = OrderImport::whereNotIn('order_import_status_id', [OrderImportStatusEnum::CANCELED, OrderImportStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();
        $importsClosed = OrderImport::where('order_import_status_id', OrderImportStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();

        $warehouseActive = WarehouseStorage::whereNotIn('warehouse_storage_status_id', [WarehouseStorageStatusEnum::CANCELED, WarehouseStorageStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();
        $warehouseClosed = WarehouseStorage::where('warehouse_storage_status_id', WarehouseStorageStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)))
            ->where('created_at', '>=', $since)->count();

        return [
            'Ordenes' => new DashboardChartData($labels, [
                new DashboardDataset(__('Orders'), [$ordersActive, $ordersClosed], $colors, $colors),
            ]),
            'Embarques' => new DashboardChartData($labels, [
                new DashboardDataset(__('Shipments'), [$shipmentsActive, $shipmentsClosed], $colors, $colors),
            ]),
            'Aduana' => new DashboardChartData($labels, [
                new DashboardDataset(__('Customs'), [$importsActive, $importsClosed], $colors, $colors),
            ]),
            'Almacen' => new DashboardChartData($labels, [
                new DashboardDataset(__('Warehouses'), [$warehouseActive, $warehouseClosed], $colors, $colors),
            ]),
        ];
    }
}
