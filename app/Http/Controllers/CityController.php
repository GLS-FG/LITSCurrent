<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityPostRequest;
use App\Http\Requests\CityPutRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $cities = City::when($search, fn ($query, $search) => $query
                ->where('name', 'like', '%' . $search . '%')
                ->orWhereHas('state', function ($query) use ($search) {
                    $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('country', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', '%' . $search . '%');

                        });
                })
            )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'asc')
            )
            ->paginate(20)->withQueryString();
        return view('city.index', [
            'cities' => $cities
        ]);
    }

    public function create()
    {
        return view('city.create', [ 'countries' => Country::select('id', 'name')->orderBy('name')->get() ]);
    }

    public function store(CityPostRequest $request)
    {
        $validated = $request->validated();
        $cityData = $request->safe()->only(['name']);
        if(isset($validated['new_state'])){
            $stateData = ['country_id' => $validated['country_id'], 'name' => $validated['new_state_name'], 'short_name' => $validated['new_state_short_name']];
            $state = State::create($stateData);
            $cityData = array_merge($cityData, [ 'state_id' => $state->id ]);
        } else {
            $cityData = $request->safe()->only(['name', 'state_id']);
        }
        $city = City::create($cityData);
        return redirect()->route('cities.show', [ 'city' => $city ]);
    }

    public function show(City $city)
    {
        return view('city.show', compact('city'));
    }

    public function edit(City $city)
    {
        return view('city.edit', [
            'city' => $city,
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(CityPutRequest $request, City $city)
    {
        $city->update($request->validated());
        return redirect()->route('cities.show', [ 'city' => $city ])
            ->with('success', 'Se actualizó correctamente la información de la ciudad.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'La ciudad fue eliminada con éxito.');
    }
}
