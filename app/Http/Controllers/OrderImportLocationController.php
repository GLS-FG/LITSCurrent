<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportLocationPostRequest;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\ServiceLocation;
use Illuminate\Support\Carbon;

class OrderImportLocationController extends Controller
{
    public function store(Order $order, OrderImport $import, ImportLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $newLocation = new ServiceLocation();
        $newLocation->service_type_status_id = $validated['service_type_status_id'];
        $newLocation->comments = $validated['comments'];
        $newLocation->location_date = $locationDate;
        $import->locations()->save($newLocation);
        return redirect()->route('orders.imports.show', [ 'order' => $order->id, 'import' => $import->id, 'activeTab' => 2 ]);
    }

    public function update(Order $order, OrderImport $import, ServiceLocation $location, ImportLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $location->service_type_status_id = $validated['service_type_status_id'];
        $location->comments = $validated['comments'];
        $location->location_date = $locationDate;
        $location->save();
        return redirect()->route('orders.imports.show', [ 'order' => $order->id, 'import' => $import->id, 'activeTab' => 2 ]);
    }

    public function destroy(Order $order, OrderImport $import, ServiceLocation $location)
    {
        $location->delete();
        return redirect()->route('orders.imports.show', [ 'order' => $order->id, 'import' => $import->id, 'activeTab' => 2 ]);
    }
}
