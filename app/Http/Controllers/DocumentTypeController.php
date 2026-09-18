<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $types = DocumentType::when($search, fn ($query, $search) => $query
                ->where('name', 'like', '%' . $search . '%')
            )
            ->orderBy('order_number')
            ->get();
        return view('document-type.index', [
            'documentTypes' => $types
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('document-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'shipments' => 'required|boolean',
            'customs' => 'required|boolean',
            'warehouse' => 'required|boolean'
        ]);
        $maxValue = DocumentType::max('order_number');
        DocumentType::create(array_merge($validatedData, ['order_number' => $maxValue + 1]));
        return redirect()->route('document-types.index')
            ->with('success', 'Se creó correctamente el tipo de documento.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentType $documentType)
    {
        return view('document-type.edit', ['documentType' => $documentType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'shipments' => 'required|boolean',
            'customs' => 'required|boolean',
            'warehouse' => 'required|boolean'
        ]);
        $documentType->update($validatedData);
        return redirect()->route('document-types.index')
            ->with('success', 'Se actualizó correctamente la información del tipo de documento.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        $documentTypeName = $documentType->name;
        $documentType->delete();
        return back()->with('success', 'El tipo de documento ' . $documentTypeName . ' ha sido eliminado.');
    }

    public function moveUp(Request $request, DocumentType $documentType)
    {
        $upRecord = DocumentType::where('order_number', '<', $documentType->order_number)->orderBy('order_number', 'desc')->first();
        $upNumber = $upRecord->order_number;
        $currentNumber = $documentType->order_number;
        $documentType->update(['order_number' => $upNumber]);
        $upRecord->update(['order_number' => $currentNumber]);
        return redirect()->route('document-types.index')
            ->with('success', 'Se actualizó correctamente el órden de los tipos de documentos.');
    }

    public function moveDown(Request $request, DocumentType $documentType)
    {
        $downRecord = DocumentType::where('order_number', '>', $documentType->order_number)->orderBy('order_number', 'asc')->first();
        $downNumber = $downRecord->order_number;
        $currentNumber = $documentType->order_number;
        $documentType->update(['order_number' => $downNumber]);
        $downRecord->update(['order_number' => $currentNumber]);
        return redirect()->route('document-types.index')
            ->with('success', 'Se actualizó correctamente el órden de los tipos de documentos.');
    }
}
