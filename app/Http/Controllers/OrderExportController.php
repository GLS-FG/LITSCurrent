<?php

namespace App\Http\Controllers;

use App\Enums\OrderExportStatusEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\OrderExportPostRequest;
use App\Http\Requests\OrderExportPutRequest;
use App\Http\Requests\OrderExportStatusPutRequest;
use App\Http\Requests\OrderProductPostRequest;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\Order;
use App\Models\OrderExport;
use App\Models\OrderExportStatus;
use App\Models\OrderProduct;
use App\Models\PetitionCode;
use App\Notifications\OrderUpdate;
use App\View\Helpers\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderExportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $orderBy = $request->get('ordering_by');
        $user = Auth::user();
        $exports = OrderExport::when($status, fn ($query, $status) => $query
                ->where('order_export_status_id', $status)
            )
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->when($search, fn ($query, $search) => $query
                ->where(function ($query) use ($search) {
                    return $query->where('reference', 'like', '%' . $search . '%')
                        ->orWhere('comments', 'like', '%' . $search . '%')
                        ->orWhereHas('order', function ($query) use ($search) {
                            return $query
                                ->where('code', 'like', '%' . $search . '%')
                                ->orWhereHas('client', function ($query) use ($search) {
                                    $query
                                        ->where('company_name', 'like', '%' . $search . '%');
                                });
                        });
                })
            )
            ->when($orderBy, fn ($query, $orderBy) => $query
                ->orderBy($orderBy, request('ordering_rule', 'desc')),
                fn ($query) => $query
                    ->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('order-export.index', [
            'exports' => $exports,
            'statuses' => OrderExportStatus::all()
        ]);
    }

    public function create(Order $order)
    {
        return view('order-export.create', [
            'order' => $order,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'statuses' => OrderExportStatus::all()
        ]);
    }

    public function store(Order $order, OrderExportPostRequest $request)
    {
        $validated = $request->validated();
        $validated = $request->safe()->except(['payment_date', 'petition_date']);
        $data = array_merge($validated, [ 'order_id' => $order->id, 'order_export_status_id' => OrderExportStatusEnum::ACTIVE ]);
        $onlyPayment = $request->safe()->only(['payment_date']);
        if (!is_null($onlyPayment['payment_date'])) {
            $paymentDate =  Carbon::createFromFormat('d/m/Y', $onlyPayment['payment_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'payment_date' => $paymentDate ]);
        }
        $onlyPetition = $request->safe()->only(['petition_date']);
        if (!is_null($onlyPetition['petition_date'])) {
            $petitionDate =  Carbon::createFromFormat('d/m/Y', $onlyPetition['petition_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'petition_date' => $petitionDate ]);
        }
        $export = OrderExport::create($data);
        return redirect()->route('orders.exports.show', [ 'order' => $order, 'export' => $export ]);
    }

    public function storeProduct(Order $order, OrderExport $export, OrderProductPostRequest $request)
    {
        $validated = $request->validated();
        $product = new OrderProduct();
        $product->order_id = $order->id;
        $product->product = $validated['product'];
        $product->dimensions = $validated['dimensions'];
        $product->weight = $validated['weight'];
        $product->container = $validated['container'];
        $product->quantity = $validated['quantity'];
        $product->value = $validated['value'];
        $product->insured = $validated['insured'];
        $export->products()->save($product);
        return back()->with('success', 'La mercancía ha sido creada.');
    }

    private function getOrderServices(Order $order, int $id): array
    {
        $services = [];
        foreach ($order->storages as $storage) {
            if (count($storage->products) > 0){
                $services[] = new Service($storage, 'Almacén', 'orders.warehouse-storages.', 'warehouse_storage', $storage->warehouse_storage_status_id, $storage->created_at);
            }
        }
        foreach ($order->imports as $import) {
            if (count($import->products) > 0){
                $services[] = new Service($import, 'Aduana', 'orders.imports.', 'import', $import->order_import_status_id, $import->created_at);
            }
        }
        foreach ($order->exports as $export) {
            if($export->id != $id && count($export->products) > 0) {
                $services[] = new Service($export, 'Exportación', 'orders.exports.', 'export', $export->order_export_status_id, $export->created_at);
            }
        }
        foreach ($order->shipments as $shipment) {
            if (count($shipment->products) > 0){
                $services[] = new Service($shipment, 'Embarque', 'orders.shipments.', 'shipment', $shipment->order_shipment_status_id, $shipment->created_at);
            }
        }
        usort($services, function ($a, $b) {
            return $a->createdAt->timestamp - $b->createdAt->timestamp;
        });
        return $services;
    }

    public function show(Order $order, OrderExport $export)
    {
        return view('order-export.show', [
            'order' => $order,
            'export' => $export,
            'statuses' => OrderExportStatus::all(),
            'services' => $this->getOrderServices($order, $export->id)
        ]);
    }

    public function edit(Order $order, OrderExport $export)
    {
        return view('order-export.edit', [
            'order' => $order,
            'export' => $export,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'statuses' => OrderExportStatus::all()
        ]);
    }

    public function update(Order $order, OrderExport $export, OrderExportPutRequest $request)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['payment_date', 'petition_date']);
        $onlyPayment = $request->safe()->only(['payment_date']);
        if (!is_null($onlyPayment['payment_date'])) {
            $paymentDate =  Carbon::createFromFormat('d/m/Y', $onlyPayment['payment_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'payment_date' => $paymentDate ]);
        }
        $onlyPetition = $request->safe()->only(['petition_date']);
        if (!is_null($onlyPetition['petition_date'])) {
            $petitionDate =  Carbon::createFromFormat('d/m/Y', $onlyPetition['petition_date'])->format('Y-m-d');
            $data = array_merge($data, [ 'petition_date' => $petitionDate ]);
        }
        $export->update($data);
        return redirect()->route('orders.exports.show', [ 'order' => $order, 'export' => $export ])
            ->with('success', 'Se actualizó correctamente la orden de exportación.');
    }

    public function updateStatus(OrderExportStatusPutRequest $request, Order $order, OrderExport $export)
    {
        $export->update($request->validated());
        return redirect()->route('orders.exports.show', [ 'order' => $order, 'export' => $export ])
            ->with('success', 'Se actualizó correctamente el estatus la orden de exportación.');
    }

    public function destroy(Order $order, OrderExport $export): RedirectResponse
    {
        $export->order_export_status_id = OrderExportStatusEnum::CANCELED;
        $export->save();
        return back()->with('success', 'La órden de exportación ' . $export->reference . ' ha sido cancelada.');
    }

    public function notify(Order $order, OrderExport $export): RedirectResponse
    {
        $user = $order->client->user;
        $updateMessage = "Estás recibibendo este email porque tu servicio de Exportación " . $export->reference . " de la orden " . $order->code . " se encuentra en estatus " . $export->order_export_status_id->label() . ".";
        $url = route('orders.exports.show', [ 'order' => $order->id, 'export' => $export->id ]);
        $user->notify(new OrderUpdate($updateMessage, $url));
        return back()->with('success', 'Se ha enviado una notificación de actualización al cliente.');
    }
}
