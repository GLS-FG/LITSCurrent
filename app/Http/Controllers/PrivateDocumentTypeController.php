<?php

namespace App\Http\Controllers;

use App\Models\PrivateDocumentType;
use Illuminate\Http\Request;

class PrivateDocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $types = PrivateDocumentType::when($search, fn ($query, $search) => $query
            ->where('name', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('private-document-type.index', [
            'documentTypes' => $types
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('private-document-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['name' => 'required|string' ]);
        PrivateDocumentType::create($validatedData);
        return redirect()->route('private-types.index')
            ->with('success', 'Se creó correctamente el tipo de documento.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrivateDocumentType $privateType)
    {
        return view('private-document-type.edit', ['documentType' => $privateType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrivateDocumentType $privateType)
    {
        $validatedData = $request->validate(['name' => 'required|string']);
        $privateType->update($validatedData);
        return redirect()->route('private-types.index')
            ->with('success', 'Se actualizó correctamente la información del tipo de documento.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrivateDocumentType $privateType)
    {
        $privateTypeName = $privateType->name;
        $privateType->delete();
        return back()->with('success', 'El tipo de documento ' . $privateTypeName . ' ha sido eliminado.');
    }
}
