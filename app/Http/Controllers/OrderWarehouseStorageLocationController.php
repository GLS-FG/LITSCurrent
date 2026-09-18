<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseLocationPostRequest;
use App\Models\Order;
use App\Models\WarehouseStorage;
use App\Models\ServiceLocation;
use Illuminate\Support\Carbon;

class OrderWarehouseStorageLocationController extends Controller
{
    public function store(Order $order, WarehouseStorage $warehouseStorage, WarehouseLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $newLocation = new ServiceLocation();
        $newLocation->service_type_status_id = $validated['service_type_status_id'];
        $newLocation->comments = $validated['comments'];
        $newLocation->location_date = $locationDate;
        $warehouseStorage->locations()->save($newLocation);
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order->id, 'warehouse_storage' => $warehouseStorage->id, 'activeTab' => 2 ]);
    }

    public function update(Order $order, WarehouseStorage $warehouseStorage, ServiceLocation $location, WarehouseLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $location->service_type_status_id = $validated['service_type_status_id'];
        $location->comments = $validated['comments'];
        $location->location_date = $locationDate;
        $location->save();
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order->id, 'warehouse_storage' => $warehouseStorage->id, 'activeTab' => 2 ]);
    }

    public function destroy(Order $order, WarehouseStorage $warehouseStorage, ServiceLocation $location)
    {
        $location->delete();
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order->id, 'warehouse_storage' => $warehouseStorage->id, 'activeTab' => 2 ]);
    }
}
