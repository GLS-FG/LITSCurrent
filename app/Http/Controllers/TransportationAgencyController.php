<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportationAgencyPostRequest;
use App\Http\Requests\TransportationAgencyPutRequest;
use App\Models\TransportationAgency;
use Illuminate\Http\Request;

class TransportationAgencyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $agencies = TransportationAgency::when($search, fn ($query, $search) => $query
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('contact_name', 'like', '%' . $search . '%')
                ->orWhere('phone1', 'like', '%' . $search . '%')
            )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('transportation-agency.index', [
            'agencies' => $agencies
        ]);
    }

    public function create()
    {
        return view('transportation-agency.create');
    }

    public function store(TransportationAgencyPostRequest $request)
    {
        $transportationAgency = TransportationAgency::create($request->validated());
        return redirect()->route('transportation-agencies.show', [ 'transportation_agency' => $transportationAgency ]);
    }

    public function show(TransportationAgency $transportationAgency)
    {
        return view('transportation-agency.show', ['agency' => $transportationAgency]);
    }

    public function edit(TransportationAgency $transportationAgency)
    {
        return view('transportation-agency.edit', ['agency' => $transportationAgency]);
    }

    public function update(TransportationAgencyPutRequest $request, TransportationAgency $transportationAgency)
    {
        $transportationAgency->update($request->validated());
        return redirect()->route('transportation-agencies.show', [ 'transportation_agency' => $transportationAgency ])
            ->with('success', 'Se actualizó correctamente la información del transportista.');
    }

    public function destroy(TransportationAgency $transportationAgency)
    {
        $agencyName = $transportationAgency->name;
        $transportationAgency->delete();
        return back()->with('success', 'La agencia ' . $agencyName . ' ha sido eliminada.');
    }
}
