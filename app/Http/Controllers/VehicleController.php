<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehiclePostRequest;
use App\Http\Requests\VehiclePutRequest;
use App\Models\TransportationAgency;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function create(TransportationAgency $transportationAgency)
    {
        return view('transportation-agency.vehicle.create', ['agency' => $transportationAgency]);
    }

    public function store(VehiclePostRequest $request, TransportationAgency $transportationAgency)
    {
        Vehicle::create(array_merge($request->validated(), ['transportation_agency_id' => $transportationAgency->id]));
        return redirect()->route('transportation-agencies.show', [ 'transportation_agency' => $transportationAgency ]);
    }

    public function edit(TransportationAgency $transportationAgency, Vehicle $vehicle)
    {
        return view('transportation-agency.vehicle.edit', ['agency' => $transportationAgency, 'vehicle' => $vehicle]);
    }

    public function update(VehiclePutRequest $request, TransportationAgency $transportationAgency, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());
        return redirect()->route('transportation-agencies.show', [ 'transportation_agency' => $transportationAgency ])
            ->with('success', 'Se actualizó correctamente la información de la unidad de transporte.');
    }

    public function destroy(TransportationAgency $transportationAgency, Vehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'La unidad de transporte ha sido eliminada.');
    }
}
