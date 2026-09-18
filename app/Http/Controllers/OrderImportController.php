<?php

namespace App\Http\Controllers;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\OrderImportPostRequest;
use App\Http\Requests\OrderImportPutRequest;
use App\Http\Requests\OrderImportStatusPutRequest;
use App\Http\Requests\OrderProductPostRequest;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\DocumentType;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\OrderImportStatus;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\PetitionCode;
use App\Models\ServiceClass;
use App\Models\ServiceTypeStatus;
use App\Notifications\OrderUpdate;
use App\View\Helpers\OrderDocumentCount;
use App\View\Helpers\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class OrderImportController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $status = $request->get('status_search');
        $user = Auth::user();
        $imports = OrderImport::whereNotIn('order_import_status_id', [OrderImportStatusEnum::CLOSED, OrderImportStatusEnum::CANCELED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);
                })
            )
            ->where(function ($query) use ($code, $reference, $client, $service, $status) {
                return $query->when($code, fn ($query, $code) => $query
                    ->where(function ($query) use ($code, $reference, $client) {
                        return $query->where('tracking_code', 'like', '%' . $code . '%')
                            ->orWhereHas('order', function ($query) use ($code) {
                                return $query->where('code', 'like', '%' . $code . '%');
                            });
                    })
                )
                    ->when($reference, fn ($query, $reference) => $query
                        ->where('reference', 'like', '%' . $reference . '%')
                    )
                    ->when($client, fn ($query, $client) => $query
                        ->where(function ($query) use ($code, $reference, $client) {
                            return $query->whereHas('order.client', function ($query) use ($client) {
                                $query
                                    ->where('company_name', 'like', '%' . $client . '%')
                                    ->orWhere('trade_name', 'like', '%' . $client . '%');
                            })
                                ->orWhereHas('order.contact', function ($query) use ($client) {
                                    $query
                                        ->where('name', 'like', '%' . $client . '%');
                                });
                        })
                    )
                    ->when($service, fn ($query, $service) => $query
                        ->where(function ($query) use ($service) {
                            return $query->whereHas('serviceClass', function ($query) use ($service) {
                                $query
                                    ->where('name', 'like', '%' . $service . '%');
                            })
                                ->orWhereHas('serviceMode', function ($query) use ($service) {
                                    $query
                                        ->where('name', 'like', '%' . $service . '%');;
                                })
                                ->orWhereHas('classType', function ($query) use ($service) {
                                    $query
                                        ->where('name', 'like', '%' . $service . '%');;
                                })
                                ->orWhereHas('serviceLevel', function ($query) use ($service) {
                                    $query
                                        ->where('name', 'like', '%' . $service . '%');;
                                });
                        })
                    )
                    ->when($status, fn ($query, $status) => $query
                        ->where(function ($query) use ($status) {
                            return $query->whereHas('latestLocation.status', function ($query) use ($status) {
                                $query
                                    ->where('name', 'like', '%' . $status . '%');
                            });
                        })
                    );
            })
            ->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();
        return view('order-import.index', [
            'imports' => $imports,
            'statuses' => OrderImportStatus::all()
        ]);
    }

    public function create(Order $order)
    {
        $serviceClasses = ServiceClass::where('service_type_id', 2)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 2)->first();
        return view('order-import.create', [
            'order' => $order,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'incoterms' => Incoterm::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
            'statuses' => OrderImportStatus::all()
        ]);
    }

    public function store(Order $order, OrderImportPostRequest $request)
    {
        $validated = $request->validated();
        $data = array_merge($validated, [ 'order_id' => $order->id, 'order_import_status_id' => OrderImportStatusEnum::ACTIVE, 'urgent' => $order->urgent]);
        $orderId = OrderImport::count() + 1;
        $code = 'GLS' . str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT) . '01' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $data = array_merge($data, [ 'tracking_code' => $code ]);
        $import = OrderImport::create($data);
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import ]);
    }

    public function storeProduct(Order $order, OrderImport $import, OrderProductPostRequest $request)
    {
        $validated = $request->validated();
        $product = new OrderProduct();
        $product->order_id = $order->id;
        $product->product = $validated['product'];
        $product->weight = $validated['weight'];
        $product->container = $validated['container'];
        $product->quantity = $validated['quantity'];
        $product->value = $validated['value'];
        $product->reference = $validated['reference'];
        $product->dimensions = $validated['dimensions'];
        $product->height = $validated['height'];
        $product->width = $validated['width'];
        $product->length = $validated['length'];
        $product->unit_measure = $validated['unit_measure'];
        $product->weight_measure = $validated['weight_measure'];
        $product->incoterm_id = $validated['incoterm_id'];
        $import->products()->save($product);
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
            if($import->id != $id && count($import->products) > 0) {
                $services[] = new Service($import, 'Aduana', 'orders.imports.', 'import', $import->order_import_status_id, $import->created_at);
            }
        }
        foreach ($order->exports as $export) {
            if (count($export->products) > 0){
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

    public function show(Order $order, OrderImport $import)
    {
        Gate::authorize('view', $import);
        $documentTypes = [];
        foreach (DocumentType::where('customs', true)->orderBy('order_number')->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $import->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $now = Carbon::now();
        $milestones = [];
        foreach ($import->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->orderBy('location_date')->get() as $group) {
            $status = ServiceTypeStatus::withTrashed()->findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        return view('order-import.show', [
            'order' => $order,
            'import' => $import,
            'now' => $now,
            'milestones' => $milestones,
            'incoterms' => Incoterm::orderBy('id', 'desc')->get(),
            'statuses' => OrderImportStatus::all(),
            'services' => $this->getOrderServices($order, $import->id),
            'documentTypes' => $documentTypes,
        ]);
    }

    public function edit(Order $order, OrderImport $import)
    {
        $serviceClasses = ServiceClass::where('service_type_id', 2)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 2)->first();
        return view('order-import.edit', [
            'order' => $order,
            'import' => $import,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'statuses' => OrderImportStatus::all(),
            'incoterms' => Incoterm::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
        ]);
    }

    public function update(Order $order, OrderImport $import, OrderImportPutRequest $request)
    {
        $data = $request->validated();
        $import->update($data);
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import ])
            ->with('success', 'Se actualizó correctamente la orden de aduana.');
    }

    public function updateStatus(OrderImportStatusPutRequest $request, Order $order, OrderImport $import)
    {
        $validated = $request->validated();
        if($validated["order_import_status_id"] == OrderImportStatusEnum::ACTIVE && $import->order_import_status_id->value != OrderImportStatusEnum::ACTIVE && $order->order_status_id != OrderStatusEnum::ACTIVE){
            return back()->withErrors(['Estatus' => 'Antes de reactivar la aduana primero debes reactivar la orden de servicio.']);
        }
        $import->update($request->validated());
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import ])
            ->with('success', 'Se actualizó correctamente el estatus la orden de aduana.');
    }

    public function destroy(Order $order, OrderImport $import): RedirectResponse
    {
        $import->order_import_status_id = OrderImportStatusEnum::CANCELED;
        $import->save();
        return back()->with('success', 'La órden de aduana ' . $import->reference . ' ha sido cancelada.');
    }

    public function notify(Order $order, OrderImport $import): RedirectResponse
    {
        $user = $order->client->user;
        $updateMessage = "Estás recibibendo este email porque tu servicio de Aduana " . $import->reference . " de la orden " . $order->code . " se encuentra en estatus " . $import->order_import_status_id->label() . ".";
        $url = route('orders.imports.show', [ 'order' => $order->id, 'import' => $import->id ]);
        $user->notify(new OrderUpdate($updateMessage, $url));
        return back()->with('success', 'Se ha enviado una notificación de actualización al cliente.');
    }

    public function printChecklist(Order $order, OrderImport $import)
    {
        $documentTypes = [];
        foreach (DocumentType::where('customs', true)->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $import->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $milestones = [];
        foreach ($import->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->get() as $group) {
            $status = ServiceTypeStatus::findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        $data = [
            'order' => $order,
            'service' => $import,
            'documentTypes' => $documentTypes,
            'milestones' => $milestones
        ];
        $pdf = Pdf::loadView('order-shipment.printchecklist', $data);
        $pdf->setOptions(['dpi' => 105]);
        $pdf->setPaper('letter');
        $filename = 'CL-' . $import->tracking_code . '.pdf';
        return $pdf->download($filename);
    }

    public function updateChecklistComments(Request $request, Order $order, OrderImport $import)
    {
        $validatedData = $request->validate([
            'checklist_comments' => 'required|string|max:500',
        ]);
        $import->update($validatedData);
        return redirect()->route('orders.imports.show', [ 'order' => $order, 'import' => $import, 'activeTab' => 3 ])
            ->with('success', 'Se actualizó correctamente el comentario del checklist.');
    }
}
