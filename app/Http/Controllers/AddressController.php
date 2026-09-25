<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressPostRequest;
use App\Http\Requests\AddressPutRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $addresses = Address::when($search, fn ($query, $search) => $query
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('nickname', 'like', '%' . $search . '%')
            ->orWhere('contact_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('address.index', [
            'addresses' => $addresses
        ]);
    }

    public function create()
    {
        return view('address.create', [
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(AddressPostRequest $request)
    {
        $address = Address::create($request->validated());
        return redirect()->route('addresses.show', [ 'address' => $address ])
            ->with('success', 'Se creó correctamente la dirección.');
    }

    public function show(Address $address)
    {
        return view('address.show', ['address' => $address]);
    }

    public function edit(Address $address)
    {
        return view('address.edit', [
            'address' => $address,
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
            'states' => State::select('id', 'name')->where('country_id', $address->country_id)->orderBy('name')->get(),
            'cities' => City::select('id', 'name')->where('state_id', $address->state_id)->orderBy('name')->get(),
        ]);
    }

    public function update(AddressPutRequest $request, Address $address)
    {
        $address->update($request->validated());
        return redirect()->route('addresses.show', [ 'address' => $address ])
            ->with('success', 'Se actualizó correctamente la información de la dirección.');
    }

    public function destroy(Address $address)
    {
        $addressName = $address->name;
        $address->delete();
        return back()->with('success', 'La dirección ' . $addressName . ' ha sido eliminada.');
    }
}
