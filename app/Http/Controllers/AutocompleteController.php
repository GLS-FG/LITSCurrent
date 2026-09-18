<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\ServiceClass;
use App\Models\State;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutocompleteController extends Controller
{
    public function countries(Request $request)
    {
        $search = $request->get('search');
        $countries = Country::select('id', 'name')
            ->where( 'name', 'like' , '%' . $search . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($this->mapLocations($countries));
    }

    public function states(Request $request)
    {
        if ($request->isNotFilled('country_id')) {
            return response()->json([]);
        }
        $country = $request->get('country_id');
        $search = $request->get('search');
        $states = State::select('id', 'name')
            ->where( 'country_id', $country)
            ->where( 'name', 'like' , '%' . $search . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($this->mapLocations($states));
    }

    public function cities(Request $request)
    {
        if ($request->isNotFilled('state_id')) {
            return response()->json([]);
        }
        $state = $request->get('state_id');
        $search = $request->get('search');
        $cities = City::select('id', 'name')
            ->where( 'state_id', $state)
            ->where( 'name', 'like' , '%' . $search . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($this->mapLocations($cities));
    }

    public function contacts(Request $request)
    {
        if ($request->isNotFilled('client_id')) {
            return response()->json([]);
        }
        $client = $request->get('client_id');
        $contacts = User::select('id', 'name')
            ->where( 'client_id', $client)
            ->orderBy('name')
            ->get();
        return response()->json($this->mapLocations($contacts));
    }

    private function mapLocations(Collection $locations)
    {
        $results = [];
        foreach ($locations as $location) {
            $results[] = ['value' => $location->id, 'label' => $location->name];
        }
        return $results;
    }

    public function addresses(Request $request)
    {
        $search = $request->get('search');
        $countries = Address::where( 'name', 'like' , '%' . $search . '%')
            ->orWhere('address', 'like' , '%' . $search . '%')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($this->mapAddresses($countries));
    }

    private function mapAddresses(Collection $addresses)
    {
        $results = [];
        foreach ($addresses as $address) {
            $fullAddress = $address->name . PHP_EOL .
                $address->address . ', ' . $address->neighborhood . PHP_EOL .
                $address->city->name . ', ' . $address->state->name. ' ' . $address->postal_code . ' ' . $address->country->name . PHP_EOL .
                $address->contact_name;
            $results[] = [
                'value' => $address->id,
                'label' => $address->name,
                'city_id' => $address->city_id,
                'state_id' => $address->state_id,
                'country_id' => $address->country_id,
                'link' => $address->link,
                'full_address' => $fullAddress,
            ];
        }
        return $results;
    }

    public function serviceTypes(Request $request)
    {
        if ($request->isNotFilled('service_type')) {
            abort(400, 'El campo requerido Service Type no está presente.');
        }
        $serviceType = $request->get('service_type');
        try {
            $responseValue = [];
            $serviceClass = ServiceClass::findOrFail($serviceType);
            foreach ($serviceClass->serviceModes as $serviceMode) {
                $serviceModeItem = ['id' => $serviceMode->id, 'name' => $serviceMode->name, 'class_types' => []];
                foreach ($serviceMode->classTypes as $classType) {
                    $classTypeItem = ['id' => $classType->id, 'name' => $classType->name, 'service_levels' => []];
                    foreach ($classType->serviceLevels as $serviceLevel) {
                        $classTypeItem['service_levels'][] = ['id' => $serviceLevel->id, 'name' => $serviceLevel->name];
                    }
                    $serviceModeItem['class_types'][] = $classTypeItem;
                }
                $responseValue[] = $serviceModeItem;
            }
            return response()->json($responseValue);
        } catch (ModelNotFoundException $e) {
            abort(400, 'El servicio ' . $serviceType . ' no existe en el catálogo de servicios.');
        }
    }

    public function vehicles(Request $request)
    {
        if ($request->isNotFilled('transportation_agency_id')) {
            return response()->json([]);
        }
        $search = $request->get('search');
        $transportation = $request->get('transportation_agency_id');
        $countries = Vehicle::select('id', 'eco_number', 'plates', 'vehicle_type')
            ->where( 'transportation_agency_id', $transportation)
            ->where( 'eco_number', 'like' , '%' . $search . '%')
            ->orderBy('eco_number')
            ->limit(20)
            ->get();
        return response()->json($this->mapVehicles($countries));
    }

    private function mapVehicles(Collection $locations)
    {
        $results = [];
        foreach ($locations as $location) {
            $results[] = [
                'value' => $location->id,
                'label' => $location->eco_number,
                'plates' => $location->plates,
                'vehicle_type' => $location->vehicle_type,
            ];
        }
        return $results;
    }
}
