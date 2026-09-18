<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Enums\TransportationStatusEnum;
use App\Http\Requests\TransportationPutRequest;
use App\Http\Requests\TransportationStatusPutRequest;
use App\Models\Incoterm;
use App\Models\Transportation;
use App\Models\TransportationAgency;
use App\Models\TransportationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransportationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $orderBy = $request->get('ordering_by');
        $user = Auth::user();
        $transportations = Transportation::when($status, fn ($query, $status) => $query
                ->where('transportation_status_id', $status)
            )
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('shipment', function ($query) use ($user) {
                    return $query->whereHas('order', function ($query) use ($user) {
                        return $query->where('client_id', $user->client->id);
                    });
                })
            )
            ->when($search, fn ($query, $search) => $query
                ->where(function ($query) use ($search) {
                    return $query->where('transportation_type', 'like', '%' . $search . '%')
                        ->orWhere('plates', 'like', '%' . $search . '%')
                        ->orWhere('driver', 'like', '%' . $search . '%')
                        ->orWhereHas('incoterm', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', '%' . $search . '%');
                        });
                })
            )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('transportation.index', [
            'transportations' => $transportations,
            'statuses' => TransportationStatus::all()
        ]);
    }

    public function show(Transportation $transportation)
    {
        return view('transportation.show', [
            'transportation' => $transportation,
            'statuses' => TransportationStatus::all()
        ]);
    }

    public function edit(Transportation $transportation)
    {
        return view('transportation.edit', [
            'transportation' => $transportation,
            'agencies' => TransportationAgency::all(),
            'incoterms' => Incoterm::all(),
            'statuses' => TransportationStatus::all()
        ]);
    }

    public function update(TransportationPutRequest $request, Transportation $transportation)
    {
        $validated = $request->validated();
        $startDate =  Carbon::createFromFormat('d/m/Y', $validated['start_date'])->format('Y-m-d');
        $endDate =  Carbon::createFromFormat('d/m/Y', $validated['end_date'])->format('Y-m-d');
        $validated = $request->safe()->except(['start_date', 'end_date']);
        $transportation->update(array_merge($validated, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));
        return redirect()->route('transportations.show', [ 'transportation' => $transportation->id ])
            ->with('success', 'Se actualizó correctamente el transporte.');
    }

    public function updateStatus(TransportationStatusPutRequest $request, Transportation $transportation)
    {
        $transportation->update($request->validated());
        return redirect()->route('transportations.show', [ 'transportation' => $transportation->id ])
            ->with('success', 'Se actualizó correctamente el estatus del transporte.');
    }

    public function destroy(Transportation $transportation)
    {
        $transportation->transportation_status_id = TransportationStatusEnum::CANCELED;
        $transportation->save();
        $shipment = $transportation->shipment;
        $shipment->transportation_id = null;
        $shipment->save();
        return back()->with('success', 'El transporte ha sido cancelada y se quitó de la órden embarcación.');
    }
}
