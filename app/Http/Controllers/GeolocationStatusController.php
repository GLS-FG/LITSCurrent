<?php

namespace App\Http\Controllers;

use App\Models\GeolocationStatus;
use Illuminate\Http\Request;

class GeolocationStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $statuses = GeolocationStatus::when($search, fn ($query, $search) => $query
            ->where('name', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('geolocation-status.index', [
            'statuses' => $statuses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('geolocation-status.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([ 'name' => 'required' ]);
        GeolocationStatus::create($validatedData);
        return redirect()->route('geolocation-statuses.index')
            ->with('success', 'Se creó correctamente el estatus.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeolocationStatus $geolocationStatus)
    {
        return view('geolocation-status.edit', ['status' => $geolocationStatus]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeolocationStatus $geolocationStatus)
    {
        $validatedData = $request->validate([ 'name' => 'required' ]);
        $geolocationStatus->update($validatedData);
        return redirect()->route('geolocation-statuses.index')
            ->with('success', 'Se actualizó correctamente la información del estatus.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeolocationStatus $geolocationStatus)
    {
        $geolocationStatusName = $geolocationStatus->name;
        $geolocationStatus->delete();
        return back()->with('success', 'El estatus ' . $geolocationStatusName . ' ha sido eliminado.');
    }
}
