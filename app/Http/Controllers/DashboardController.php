<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\OrderShipment;
use App\Models\WarehouseStorage;
use App\View\Helpers\DashboardStat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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

        return view('dashboard.dashboard', [
            'stats' => $stats,
            'attentionItems' => $this->attentionItems($orders, $shipments, $imports, $storages),
        ]);
    }

    /**
     * Pulls active records flagged as urgent across all 4 entity types into a
     * single "needs your attention" list for the dashboard.
     *
     * @return array<int, array<string, mixed>>
     */
    private function attentionItems(Builder $orders, Builder $shipments, Builder $imports, Builder $storages): array
    {
        $items = collect();

        $items = $items->merge(
            (clone $orders)->where('urgent', true)->limit(6)->get()->map(fn (Order $order) => [
                'slug' => 'order',
                'icon' => 'fa-regular fa-clipboard-list-check',
                'type' => __('Order'),
                'reference' => $order->reference ?: $order->code ?: ('#' . $order->id),
                'created_at' => $order->created_at,
                'route' => route('orders.show', $order->id),
            ])
        );

        $items = $items->merge(
            (clone $shipments)->where('urgent', true)->with('order')->limit(6)->get()->map(fn (OrderShipment $shipment) => [
                'slug' => 'shipment',
                'icon' => 'fa-regular fa-route',
                'type' => __('Shipment'),
                'reference' => $shipment->reference ?: ('#' . $shipment->id),
                'created_at' => $shipment->created_at,
                'route' => route('orders.shipments.show', [$shipment->order_id, $shipment->id]),
            ])
        );

        $items = $items->merge(
            (clone $imports)->where('urgent', true)->with('order')->limit(6)->get()->map(fn (OrderImport $import) => [
                'slug' => 'import',
                'icon' => 'fa-regular fa-person-military-pointing',
                'type' => __('Custom'),
                'reference' => $import->reference ?: ('#' . $import->id),
                'created_at' => $import->created_at,
                'route' => route('orders.imports.show', [$import->order_id, $import->id]),
            ])
        );

        $items = $items->merge(
            (clone $storages)->where('urgent', true)->with('order')->limit(6)->get()->map(fn (WarehouseStorage $storage) => [
                'slug' => 'warehouse_storage',
                'icon' => 'fa-regular fa-warehouse',
                'type' => __('Warehouse'),
                'reference' => $storage->reference ?: ('#' . $storage->id),
                'created_at' => $storage->created_at,
                'route' => route('orders.warehouse-storages.show', [$storage->order_id, $storage->id]),
            ])
        );

        return $items->sortByDesc('created_at')->take(6)->values()->all();
    }
}
