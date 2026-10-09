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
            'attentionItems' => $this->attentionItems($orders),
            'pending' => $this->operatorPending($user),
        ]);
    }

    /**
     * Counts for the "Your pending items" panel: only the orders the user created.
     * System users only; clients get null and the panel is not rendered.
     *
     * @return array<string, int>|null
     */
    private function operatorPending($user): ?array
    {
        if ($user->hasRole(RolesEnum::CLIENT)) {
            return null;
        }

        return [
            'mine' => Order::active()->where('user_id', $user->id)->count(),
            'overdue' => OrderShipment::active()
                ->whereDate('estimated_time_arrival', '<', today())
                ->whereHas('order', fn ($order) => $order->where('user_id', $user->id))
                ->count(),
            'stale' => Order::active()->where('user_id', $user->id)->staleFor(OrderController::STALE_DAYS)->count(),
            'staleDays' => OrderController::STALE_DAYS,
        ];
    }

    /**
     * Active orders flagged as urgent, for the "needs your attention" list.
     * Only orders: their services belong to the same order, so listing them
     * separately would repeat the same item.
     *
     * @return array<int, array<string, mixed>>
     */
    private function attentionItems(Builder $orders): array
    {
        return (clone $orders)->where('urgent', true)->limit(6)->get()->map(fn (Order $order) => [
            'slug' => 'order',
            'icon' => 'fa-regular fa-clipboard-list-check',
            'type' => __('Order'),
            'reference' => $order->reference ?: $order->code ?: ('#' . $order->id),
            'created_at' => $order->created_at,
            'route' => route('orders.show', $order->id),
        ])->all();
    }
}
