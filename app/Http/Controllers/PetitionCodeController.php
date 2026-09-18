<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetitionCodePostRequest;
use App\Http\Requests\PetitionCodePutRequest;
use App\Models\PetitionCode;
use Illuminate\Http\Request;

class PetitionCodeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $petitionCodes = PetitionCode::when($search, fn ($query, $search) => $query
            ->where('code', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
        )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('petition-code.index', [
            'codes' => $petitionCodes
        ]);
    }

    public function create()
    {
        return view('petition-code.create');
    }

    public function store(PetitionCodePostRequest $request)
    {
        $petitionCode = PetitionCode::create($request->validated());
        return redirect()->route('petition-codes.show', [ 'petition_code' => $petitionCode ]);
    }

    public function show(PetitionCode $petitionCode)
    {
        return view('petition-code.show', ['code' => $petitionCode]);
    }

    public function edit(PetitionCode $petitionCode)
    {
        return view('petition-code.edit', ['code' => $petitionCode]);
    }

    public function update(PetitionCodePutRequest $request, PetitionCode $petitionCode)
    {
        $petitionCode->update($request->validated());
        return redirect()->route('petition-codes.show', [ 'petition_code' => $petitionCode ])
            ->with('success', 'Se actualizó correctamente la información de la clave de pedimento.');
    }

    public function destroy(PetitionCode $petitionCode)
    {
        $petitionCodeName = $petitionCode->description;
        $petitionCode->delete();
        return back()->with('success', 'La clave de pedimento ' . $petitionCodeName . ' ha sido eliminada.');
    }
}
