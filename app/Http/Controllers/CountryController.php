<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryPostRequest;
use App\Http\Requests\CountryPutRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $countries = Country::when(
            $search,
            fn ($query, $search) => $query
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%')
        )->paginate(20);
        return view('country.index', [
            'countries' => $countries
        ]);
    }

    public function create()
    {
        return view('country.create');
    }

    public function store(CountryPostRequest $request)
    {
        $country = Country::create($request->validated());
        return redirect()->route('countries.show', [ 'country' => $country ]);
    }

    public function show(Country $country)
    {
        return view('country.show', compact('country'));
    }

    public function edit(Country $country)
    {
        return view('country.edit', compact('country'));
    }

    public function update(CountryPutRequest $request, Country $country)
    {
        $country->update($request->validated());
        return redirect()->route('countries.show', [ 'country' => $country ])
            ->with('success', 'Se actualizó correctamente la información del país.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return back()->with('success', 'El país fue eliminado con éxito.');
    }
}
