<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationGeolocationPostRequest;
use App\Models\Geolocation;
use App\Models\GeolocationStatus;
use App\Models\ShipmentLocation;
use App\Models\Transportation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransportationGeolocationController extends Controller
{
    public function create(Transportation $transportation)
    {
        $now = Carbon::now();
        return view('transportation.geolocation.create', [
            'transportation' => $transportation,
            'now' => $now,
            'statuses' => GeolocationStatus::all()
        ]);
    }

    public function store(Transportation $transportation, TransportationGeolocationPostRequest $request)
    {
        $validated = $request->validated();
        $locationDate =  Carbon::createFromFormat('Y-m-d\TH:i', $validated['location_date'])->format('Y-m-d H:i:s');
        $geolocationValidated = $request->safe()->except(['location_date', 'geolocation_status_id', 'tracking_type']);
        $shipmentLocationValidated = $request->safe()->only(['geolocation_status_id', 'tracking_type']);
        $geolocation = array_merge($geolocationValidated, ['location_date' => $locationDate,  'transportation_id' => $transportation->id]);
        $shipmentLocation = array_merge($shipmentLocationValidated, ['order_shipment_id' => $transportation->shipment->id, 'ended' => false]);
        DB::transaction(function () use ($geolocation, $shipmentLocation) {
            $newGeolocation = Geolocation::create($geolocation);
            ShipmentLocation::create(array_merge($shipmentLocation, ['geolocation_id' => $newGeolocation->id ]));
        }, 5);
        return redirect()->route('transportations.show', [ 'transportation' => $transportation->id ]);
    }
}
