<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomPostRequest;
use App\Http\Requests\CustomPutRequest;
use App\Models\Country;
use App\Models\Custom;
use Illuminate\Http\Request;

class CustomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $customs = Custom::when($search, fn ($query, $search) => $query
            ->where('denomination', 'like', '%' . $search . '%')
            ->orWhere('code', 'like', '%' . $search . '%')
            ->orWhere('section', 'like', '%' . $search . '%')
            ->orWhereHas('country', function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('state', function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('city', function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%');
            })
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('custom.index', [
            'customs' => $customs
        ]);
    }

    public function create()
    {
        return view('custom.create', [ 'countries' => Country::select('id', 'name')->orderBy('name')->get() ]);
    }

    public function store(CustomPostRequest $request)
    {
        $custom = Custom::create($request->validated());
        return redirect()->route('customs.show', [ 'custom' => $custom ]);
    }

    public function show(Custom $custom)
    {
        return view('custom.show', compact('custom'));
    }

    public function edit(Custom $custom)
    {
        return view('custom.edit', [
            'custom' => $custom,
            'countries' => Country::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(CustomPutRequest $request, Custom $custom)
    {
        $custom->update($request->validated());
        return redirect()->route('customs.show', [ 'custom' => $custom ])
            ->with('success', 'Se actualizó correctamente la información de la aduana.');
    }

    public function destroy(Custom $custom)
    {
        $customName = $custom->denomination;
        $custom->delete();
        return back()->with('success', 'La aduana ' . $customName . ' ha sido eliminada.');
    }
}
