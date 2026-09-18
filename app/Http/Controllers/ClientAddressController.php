<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientAddressPostRequest;
use App\Http\Requests\ClientAddressPutRequest;
use App\Models\Address;
use App\Models\Client;

class ClientAddressController extends Controller
{
    public function create(Client $client)
    {
        return view('client.address.create', [ 'client' => $client ]);
    }

    public function store(Client $client, ClientAddressPostRequest $request)
    {
        $validated = $request->validated();
        $address = new Address;
        $address->contact_name = $validated['contact_name'];
        $address->name = $client->company_name;
        $address->nickname = $validated['nickname'];
        $address->email = $validated['email'];
        $address->phone = $validated['phone'];
        $address->address = $validated['address'];
        $address->neighborhood = $validated['neighborhood'];
        $address->postal_code = $validated['postal_code'];
        $address->city_id = $validated['city_id'];
        $address->state_id = $validated['state_id'];
        $address->country_id = $validated['country_id'];
        $client->addresses()->save($address);
        return redirect()->route('clients.show', [ 'client' => $client ])
            ->with('success', 'Se creó correctamente la dirección del cliente.');
    }

    public function edit(Client $client, Address $address)
    {
        return view('client.address.edit', [ 'client' => $client, 'address' => $address ]);
    }

    public function update(ClientAddressPutRequest $request, Client $client, Address $address)
    {
        $address->update($request->validated());
        return redirect()->route('clients.show', [ 'client' => $client ])
            ->with('success', 'Se actualizó correctamente la dirección del cliente.');
    }

    public function destroy(Client $client, Address $address)
    {
        $address->delete();
        return back()->with('success', 'La dirección del cliente ha sido eliminada correctamente.');
    }
}
