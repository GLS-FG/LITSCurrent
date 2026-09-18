<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomDeclarationInspectionPutRequest;
use App\Http\Requests\CustomDeclarationPostRequest;
use App\Http\Requests\CustomDeclarationPutRequest;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\CustomDeclaration;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderImport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CustomDeclarationController extends Controller
{
    public function create(Order $order, OrderImport $import, )
    {
        return view('order-import.declaration.create', [
            'order' => $order,
            'import' => $import,
            'customs' => Custom::where('country_id', $import->serviceClass->customs_country_id)->get(),
            'agents' => CustomAgent::all(),
            'incoterms' => Incoterm::all()
        ]);
    }

    public function store(CustomDeclarationPostRequest $request, Order $order, OrderImport $import)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['entry_date', 'draft_date', 'paid_date']);
        $onlyEntry = $request->safe()->only(['entry_date']);
        if (!is_null($onlyEntry['entry_date'])) {
            $entryDate =  Carbon::createFromFormat('d/m/Y', $onlyEntry['entry_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'entry_date' => $entryDate ]);
        }
        $onlyDraft = $request->safe()->only(['draft_date']);
        if (!is_null($onlyDraft['draft_date'])) {
            $draftDate =  Carbon::createFromFormat('d/m/Y', $onlyDraft['draft_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'draft_date' => $draftDate ]);
        }
        $onlyPayment = $request->safe()->only(['paid_date']);
        if (!is_null($onlyPayment['paid_date'])) {
            $paymentDate =  Carbon::createFromFormat('d/m/Y', $onlyPayment['paid_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'paid_date' => $paymentDate ]);
        }
        CustomDeclaration::create(array_merge($data, ['order_import_id' => $import->id]));
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import,  ])
            ->with('success', 'Se creó correctamente la declaración.');
    }

    public function edit(Order $order, OrderImport $import, CustomDeclaration $declaration)
    {
        return view('order-import.declaration.edit', [
            'order' => $order,
            'import' => $import,
            'declaration' => $declaration,
            'custom' => $declaration->custom,
            'agent' => $declaration->customAgent,
            'customs' => Custom::where('country_id', $import->serviceClass->customs_country_id)->get(),
            'agents' => CustomAgent::all(),
            'incoterms' => Incoterm::all()
        ]);
    }

    public function update(CustomDeclarationPutRequest $request, Order $order, OrderImport $import, CustomDeclaration $declaration)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['entry_date', 'draft_date', 'paid_date']);
        $onlyEntry = $request->safe()->only(['entry_date']);
        if (!is_null($onlyEntry['entry_date'])) {
            $entryDate =  Carbon::createFromFormat('d/m/Y', $onlyEntry['entry_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'entry_date' => $entryDate ]);
        }
        $onlyDraft = $request->safe()->only(['draft_date']);
        if (!is_null($onlyDraft['draft_date'])) {
            $draftDate =  Carbon::createFromFormat('d/m/Y', $onlyDraft['draft_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'draft_date' => $draftDate ]);
        }
        $onlyPayment = $request->safe()->only(['paid_date']);
        if (!is_null($onlyPayment['paid_date'])) {
            $paymentDate =  Carbon::createFromFormat('d/m/Y', $onlyPayment['paid_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'paid_date' => $paymentDate ]);
        }
        $declaration->update($data);
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import,  ])
            ->with('success', 'Se actualizó correctamente la información de la declaración aduanal.');
    }

    public function updateInspection(CustomDeclarationInspectionPutRequest $request, Order $order, OrderImport $import, CustomDeclaration $declaration)
    {
        $validated = $request->validated();
        $data = $request->safe()->only(['inspection']);
        $declaration->update($data);
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import,  ])
            ->with('success', 'Se actualizó correctamente la información de la declaración aduanal.');
    }

    public function destroy(Order $order, OrderImport $import, CustomDeclaration $declaration)
    {
        $declaration->delete();
        return back()->with('success', 'La declaración aduanal ha sido eliminada.');
    }
}
