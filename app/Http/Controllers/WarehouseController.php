<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehousePostRequest;
use App\Http\Requests\WarehousePutRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $orderBy = $request->get('ordering_by');
        $warehouses = Warehouse::when($search, fn ($query, $search) => $query
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
        return view('warehouse.index', [
            'warehouses' => $warehouses
        ]);
    }

    public function create()
    {
        return view('warehouse.create');
    }

    public function store(WarehousePostRequest $request)
    {
        $warehouse = Warehouse::create($request->validated());
        return redirect()->route('warehouses.show', [ 'warehouse' => $warehouse ]);
    }

    public function show(Warehouse $warehouse)
    {
        return view('warehouse.show', ['warehouse' => $warehouse]);
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouse.edit', ['warehouse' => $warehouse]);
    }

    public function update(WarehousePutRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());
        return redirect()->route('warehouses.show', [ 'warehouse' => $warehouse ])
            ->with('success', 'Se actualizó correctamente la información del almacen.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouseName = $warehouse->name;
        $warehouse->delete();
        return back()->with('success', 'El almacen ' . $warehouseName . ' ha sido eliminado.');
    }
}
