<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationGeolocationPostRequest;
use App\Models\Geolocation;
use App\Models\GeolocationStatus;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\ShipmentLocation;
use App\Models\Transportation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentGeolocationController extends Controller
{
    public function store(Order $order, OrderShipment $shipment, TransportationGeolocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $geolocationValidated = $request->safe()->except(['location_date', 'geolocation_status_id']);
        $shipmentLocationValidated = $request->safe()->only(['geolocation_status_id', 'tracking_type']);
        $geolocation = array_merge($geolocationValidated, ['location_date' => $locationDate]);
        $shipmentLocation = array_merge($shipmentLocationValidated, ['order_shipment_id' => $shipment->id]);
        DB::transaction(function () use ($geolocation, $shipmentLocation) {
            $newGeolocation = Geolocation::create($geolocation);
            ShipmentLocation::create(array_merge($shipmentLocation, ['geolocation_id' => $newGeolocation->id ]));
        }, 5);
        if($shipmentLocation['geolocation_status_id'] == 1) {
            $shipment->start_date = Carbon::now();
            $shipment->save();
        }
        if($shipmentLocation['geolocation_status_id'] == 20) {
            $shipment->end_date = Carbon::now();
            $shipment->save();
        }
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id, 'activeTab' => 2 ]);
    }

    public function update(Order $order, OrderShipment $shipment, Geolocation $geolocation, TransportationGeolocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $geolocationValidated = $request->safe()->except(['geolocation_status_id']);
        $geolocationData = array_merge($geolocationValidated, ['location_date' => $locationDate]);
        $geolocation->update($geolocationData);
        $geolocationStatus = $request->safe()->only(['geolocation_status_id']);
        $geolocation->shipmentLocation->update($geolocationStatus);
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id, 'activeTab' => 2 ]);
    }
}
