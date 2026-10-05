<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientPostRequest;
use App\Http\Requests\ClientPutRequest;
use App\Models\Client;
use App\Models\Country;
use App\Models\ClientAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $clients = Client::when($search, fn ($query, $search) => $query
                ->where('company_name', 'like', '%' . $search . '%')
                ->orWhere('trade_name', 'like', '%' . $search . '%')
                ->orWhere('federal_tax_id', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone1', 'like', '%' . $search . '%')
            )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('client.index', [
            'clients' => $clients
        ]);
    }

    public function create()
    {
        return view('client.create', [ 'countries' => Country::select('id', 'name')->orderBy('name')->get() ]);
    }

    public function store(ClientPostRequest $request)
    {
        $validated = $request->validated();
        $newClientData = $request->safe()->only(['company_name', 'trade_name', 'federal_tax_id', 'national_id', 'email', 'phone1']);
        $newClientAddressData = $request->safe()->only(['address_type', 'street_name', 'street_no', 'neighborhood', 'postal_code', 'city_id', 'state_id', 'country_id']);
        $client = DB::transaction(function () use ($newClientData, $newClientAddressData) {
            $newClient = Client::create($newClientData);
            ClientAddress::create(array_merge($newClientAddressData, ['client_id' => $newClient->id ]));
            return $newClient;
        }, 5);
        return redirect()->route('clients.show', [ 'client' => $client->id ])
            ->with('success', 'Se creó correctamente el cliente.');
    }

    public function show(Client $client)
    {
        return view('client.show', [
            'client' => $client
        ]);
    }

    public function edit(Client $client)
    {
        $url = Storage::url($client->image);
        return view('client.edit', [ 'client' => $client, 'profileImage' => $url ]);
    }

    public function update(ClientPutRequest $request, Client $client)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('logos');
            $client->update(array_merge($request->safe()->except(['image']), ['image' => $path]));
        } else {
            $client->update($request->safe()->except(['image']));
        }
        return redirect()->route('clients.show', [ 'client' => $client ])
            ->with('success', 'Se actualizó correctamente la información del cliente.');
    }

    public function destroy(Client $client)
    {
        $name = $client->company_name;
        $client->delete();
        return back()->with('success', 'El cliente ' . $name . ' ha sido eliminado.');
    }

    public function logo(string $filename)
    {
        $path = 'logos/' . str_replace("_",".", $filename);
        if(!Storage::exists($path)) {
            abort(404);
        }
        $file = storage_path('app/private/' . $path);
        return response()->file($file);
    }
}
