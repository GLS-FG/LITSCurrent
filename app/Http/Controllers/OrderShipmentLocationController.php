<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentLocationPostRequest;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\ServiceLocation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OrderShipmentLocationController extends Controller
{
    public function store(Order $order, OrderShipment $shipment, ShipmentLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $newLocation = new ServiceLocation();
        $newLocation->service_type_status_id = $validated['service_type_status_id'];
        $newLocation->name = $validated['name'];
        $newLocation->latitude = $validated['latitude'];
        $newLocation->longitude = $validated['longitude'];
        $newLocation->comments = $validated['comments'];
        $newLocation->location_date = $locationDate;
        $shipment->locations()->save($newLocation);
        if($newLocation->service_type_status_id == 1 || $newLocation->service_type_status_id == 21) {
            $shipment->start_date = $newLocation->location_date;
            $shipFrom = $shipment->shipFrom;
            if($shipFrom != null) {
                $newLocation->name = $shipFrom->location_name;
                $newLocation->latitude = $shipFrom->latitude;
                $newLocation->longitude = $shipFrom->longitude;
                $newLocation->save();
            }
        }
        if($newLocation->service_type_status_id == 20 || $newLocation->service_type_status_id == 37) {
            $shipment->end_date = $newLocation->location_date;
            $shipTo = $shipment->shipTo;
            if($shipTo != null) {
                $newLocation->name = $shipTo->location_name;
                $newLocation->latitude = $shipTo->latitude;
                $newLocation->longitude = $shipTo->longitude;
                $newLocation->save();
            }
        }
        $shipment->petition_date = Carbon::now();
        $shipment->save();
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id, 'activeTab' => 2 ]);
    }

    public function update(Order $order, OrderShipment $shipment, ServiceLocation $location, ShipmentLocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $location->service_type_status_id = $validated['service_type_status_id'];
        $location->name = $validated['name'];
        $location->latitude = $validated['latitude'];
        $location->longitude = $validated['longitude'];
        $location->comments = $validated['comments'];
        $location->location_date = $locationDate;
        $location->save();
        if($location->service_type_status_id == 1) {
            $shipment->start_date = $location->location_date;
            $shipment->save();
        }
        if($location->service_type_status_id == 20) {
            $shipment->end_date = $location->location_date;
            $shipment->save();
        }
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id, 'activeTab' => 2 ]);
    }

    public function destroy(Order $order, OrderShipment $shipment, ServiceLocation $location)
    {
        $location->delete();
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id, 'activeTab' => 2 ]);
    }
}
