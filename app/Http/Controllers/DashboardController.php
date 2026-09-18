<?php

namespace App\Http\Controllers;

use App\Enums\OrderExportStatusEnum;
use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Models\Order;
use App\Models\OrderExport;
use App\Models\OrderImport;
use App\Models\OrderShipment;
use App\Models\WarehouseStorage;
use App\View\Helpers\DashboardChartData;
use App\View\Helpers\DashboardDataset;
use App\View\Helpers\DashboardStat;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        $orders = Order::active()->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query->where('client_id', $user->client->id)->where('contact_id', $user->id))->orderBy('created_at', 'desc');
        $imports = OrderImport::active()->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query->whereHas('order', function ($query) use ($user) {return $query->where('client_id', $user->client->id)->where('contact_id', $user->id);}))->orderBy('created_at', 'desc');
        $shipments = OrderShipment::active()->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query->whereHas('order', function ($query) use ($user) {return $query->where('client_id', $user->client->id)->where('contact_id', $user->id);}))->orderBy('created_at', 'desc');
        $storages = WarehouseStorage::active()->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query->whereHas('order', function ($query) use ($user) {return $query->where('client_id', $user->client->id)->where('contact_id', $user->id);}))->orderBy('created_at', 'desc');
        $stats = [];
        $stats[] = new DashboardStat(__('Orders'), $orders->count(), route('orders.index'), $orders->get(), 'order', 'orders.show', 'fa-regular fa-clipboard-list-check');
        $stats[] = new DashboardStat(__('Shipments'), $shipments->count(), route('shipments.index'), $shipments->get(), 'shipment', 'orders.shipments.show', 'fa-regular fa-route');
        $stats[] = new DashboardStat(__('Customs'), $imports->count(), route('imports.index'), $imports->get(), 'import', 'orders.imports.show', 'fa-regular fa-person-military-pointing');
        $stats[] = new DashboardStat(__('Warehouses'), $storages->count(), route('warehouse-storages.index'), $storages->get(), 'warehouse_storage', 'orders.warehouse-storages.show', 'fa-regular fa-warehouse');

        $reportType = 'month';
        if($request->has('reports')) {
            $reportType = $request->get('reports');
        }
        $lineChartLabels = [];
        $lineChartDatasets = [];
        $lineChartDatasets[] = new DashboardDataset(__('Orders'), [], "#00C0EF", "#00C0EF");
        $lineChartDatasets[] = new DashboardDataset(__('Shipments'), [], "#0173B7", "#0173B7");
        $lineChartDatasets[] = new DashboardDataset(__('Customs'), [], "#00A65A", "#00A65A");
        $lineChartDatasets[] = new DashboardDataset(__('Warehouses'), [], "#F39C12", "#F39C12");
        $currentDate = Carbon::now();
        $limit = 4;
        $dateLabelFormat = '[' . __('Week') . '] W';
        if($reportType === 'semester'){
            $limit = 6;
            $dateLabelFormat = 'MMMM';
        } else if($reportType === 'all-time'){
            $limit = $currentDate->year - 2018;
            $dateLabelFormat = 'Y';
        }
        for ($i = 0; $i < $limit; $i++) {
            $dateLabel = ucfirst($currentDate->isoFormat($dateLabelFormat));
            $dateStart = $currentDate->copy();
            $dateEnd = $currentDate->copy();
            if($reportType === 'semester'){
                $dateStart->startOfMonth();
                $dateEnd->endOfMonth();
            } else if($reportType === 'all-time'){
                $dateStart->startOfYear();
                $dateEnd->endOfYear();
            } else {
                $dateStart->startOfWeek();
                $dateEnd->endOfWeek();
            }
            array_unshift($lineChartLabels, $dateLabel);
            $ordersCount = Order::where('order_status_id', '<>', OrderStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->where('client_id', $user->client->id)
                    ->where('contact_id', $user->id)
                )
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])->count();
            $shipmentsCount = OrderShipment::where('order_shipment_status_id', '<>', OrderShipmentStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', function ($query) use ($user) {
                        return $query->where('client_id', $user->client->id)
                            ->where('contact_id', $user->id);
                    })
                )
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])->count();
            $importsCount = OrderImport::where('order_import_status_id', '<>', OrderImportStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', function ($query) use ($user) {
                        return $query->where('client_id', $user->client->id)
                            ->where('contact_id', $user->id);
                    })
                )
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])->count();
            $warehouseCount = WarehouseStorage::where('warehouse_storage_status_id', '<>', WarehouseStorageStatusEnum::CANCELED)
                ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                    ->whereHas('order', function ($query) use ($user) {
                        return $query->where('client_id', $user->client->id)
                            ->where('contact_id', $user->id);
                    })
                )
                ->whereBetween('created_at', [$dateStart->toDateTimeString(), $dateEnd->toDateTimeString()])->count();
            array_unshift($lineChartDatasets[0]->data, $ordersCount);
            array_unshift($lineChartDatasets[1]->data, $shipmentsCount);
            array_unshift($lineChartDatasets[2]->data, $importsCount);
            array_unshift($lineChartDatasets[3]->data, $warehouseCount);
            if($reportType === 'semester'){
                $currentDate->subMonth();
            } else if($reportType === 'all-time'){
                $currentDate->subYear();
            } else {
                $currentDate->subWeek();
            }
        }
        $areaChartData = new DashboardChartData($lineChartLabels, $lineChartDatasets);

        $doughnutChartLabels = [__('Active'), __('Closed')];
        $ordersActiveCount = Order::whereNotIn('order_status_id', [OrderStatusEnum::CANCELED, OrderStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id)
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $ordersClosedCount = Order::where('order_status_id', OrderStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id)
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $doughnutChartDatasetOrders[] = new DashboardDataset(__('Orders'), [$ordersActiveCount, $ordersClosedCount], ["#F39C12", "#00A65A"], ["#F39C12", "#00A65A"]);
        $doughnutChartDataOrders = new DashboardChartData($doughnutChartLabels, $doughnutChartDatasetOrders);

        $shipmentsActiveCount = OrderShipment::whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CANCELED, OrderShipmentStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $shipmentsClosedCount = OrderShipment::where('order_shipment_status_id', OrderShipmentStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $doughnutChartDatasetShipments[] = new DashboardDataset(__('Shipments'), [$shipmentsActiveCount, $shipmentsClosedCount], ["#F39C12", "#00A65A"], ["#F39C12", "#00A65A"]);
        $doughnutChartDataShipments = new DashboardChartData($doughnutChartLabels, $doughnutChartDatasetShipments);

        $importsActiveCount = OrderImport::whereNotIn('order_import_status_id', [OrderImportStatusEnum::CANCELED, OrderImportStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $importsClosedCount = OrderImport::where('order_import_status_id', OrderImportStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $doughnutChartDatasetImports[] = new DashboardDataset(__('Customs'), [$importsActiveCount, $importsClosedCount], ["#F39C12", "#00A65A"], ["#F39C12", "#00A65A"]);
        $doughnutChartDataImports = new DashboardChartData($doughnutChartLabels, $doughnutChartDatasetImports);

        $warehouseActiveCount = WarehouseStorage::whereNotIn('warehouse_storage_status_id', [WarehouseStorageStatusEnum::CANCELED, WarehouseStorageStatusEnum::CLOSED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $warehouseClosedCount = WarehouseStorage::where('warehouse_storage_status_id', WarehouseStorageStatusEnum::CLOSED)
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where('created_at', ">=", $currentDate->toDateTimeString())->count();
        $doughnutChartDatasetWarehouse[] = new DashboardDataset(__('Warehouses'), [$warehouseActiveCount, $warehouseClosedCount], ["#F39C12", "#00A65A"], ["#F39C12", "#00A65A"]);
        $doughnutChartDataWarehouse = new DashboardChartData($doughnutChartLabels, $doughnutChartDatasetWarehouse);

        return view('dashboard.dashboard', [
            'stats' => $stats,
            'areaChartData' => $areaChartData,
            'doughnutChartDataOrders' => $doughnutChartDataOrders,
            'doughnutChartDataShipments' => $doughnutChartDataShipments,
            'doughnutChartDataImports' => $doughnutChartDataImports,
            'doughnutChartDataWarehouse' => $doughnutChartDataWarehouse,
        ]);
    }
}
