<?php

namespace App\Http\Controllers;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Http\Requests\OrderProductPostRequest;
use App\Http\Requests\WarehouseStoragePostRequest;
use App\Http\Requests\WarehouseStoragePutRequest;
use App\Http\Requests\WarehouseStorageStatusPutRequest;
use App\Models\DocumentType;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderImport;
use App\Models\OrderProduct;
use App\Models\ServiceClass;
use App\Models\ServiceTypeStatus;
use App\Models\Warehouse;
use App\Models\WarehouseStorage;
use App\Models\WarehouseStorageStatus;
use App\Notifications\OrderUpdate;
use App\View\Helpers\OrderDocumentCount;
use App\View\Helpers\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderWarehouseStorageController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $status = $request->get('status_search');
        $user = Auth::user();
        $storages = WarehouseStorage::whereNotIn('warehouse_storage_status_id', [WarehouseStorageStatusEnum::CLOSED, WarehouseStorageStatusEnum::CANCELED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->whereHas('order', function ($query) use ($user) {
                    return $query->where('client_id', $user->client->id)
                        ->where('contact_id', $user->id);;
                })
            )
            ->where(function ($query) use ($code, $reference, $client, $service, $status) {
                return $query->when($code, fn ($query, $code) => $query
                    ->where(function ($query) use ($code) {
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
                        ->where(function ($query) use ($client) {
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
        return view('warehouse-storage.index', [
            'storages' => $storages,
            'statuses' => WarehouseStorageStatus::all()
        ]);
    }

    public function create(Order $order)
    {
        $serviceClasses = ServiceClass::where('service_type_id', 3)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 3)->first();
        return view('warehouse-storage.create', [
            'order' => $order,
            'warehouses' => Warehouse::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
            'statuses' => WarehouseStorageStatus::all()
        ]);
    }

    public function store(Order $order, WarehouseStoragePostRequest $request)
    {
        $validated = $request->validated();
        $validated = $request->safe()->except(['receipt_date', 'document_date']);
        $data = array_merge($validated, [ 'order_id' => $order->id, 'warehouse_storage_status_id' => WarehouseStorageStatusEnum::ACTIVE, 'urgent' => $order->urgent ]);
        $data = $this->addDateToDataIfPresent($data, $request, 'receipt_date');
        $data = $this->addDateToDataIfPresent($data, $request, 'document_date');
        $orderId = WarehouseStorage::whereHas('order', function ($query) use ($order) {
            return $query->where('client_id', $order->client_id);
        })->count() + 1;
        $code = 'GLS' . str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT) . '03' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $data = array_merge($data, [ 'tracking_code' => $code ]);
        $warehouseStorage = WarehouseStorage::create($data);
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order, 'warehouse_storage' => $warehouseStorage, 'warehouse_storage_status_id' => WarehouseStorageStatusEnum::ACTIVE ]);
    }

    public function storeProduct(Order $order, WarehouseStorage $warehouseStorage, OrderProductPostRequest $request)
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
        $warehouseStorage->products()->save($product);
        return back()->with('success', 'La mercancía ha sido creada.');
    }

    public function show(Order $order, WarehouseStorage $warehouseStorage)
    {
        Gate::authorize('view', $warehouseStorage);
        $documentTypes = [];
        foreach (DocumentType::where('warehouse', true)->orderBy('order_number')->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $warehouseStorage->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $milestones = [];
        foreach ($warehouseStorage->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->orderBy('location_date')->get() as $group) {
            $status = ServiceTypeStatus::findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        $now = Carbon::now();
        return view('warehouse-storage.show', [
            'order' => $order,
            'storage' => $warehouseStorage,
            'now' => $now,
            'milestones' => $milestones,
            'incoterms' => Incoterm::orderBy('id', 'desc')->get(),
            'statuses' => WarehouseStorageStatus::all(),
            'services' => $this->getOrderServices($order, $warehouseStorage->id),
            'documentTypes' => $documentTypes
        ]);
    }

    public function edit(Order $order, WarehouseStorage $warehouseStorage)
    {
        $serviceClasses = ServiceClass::where('service_type_id', 3)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 3)->first();
        return view('warehouse-storage.edit', [
            'order' => $order,
            'storage' => $warehouseStorage,
            'warehouses' => Warehouse::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
            'statuses' => WarehouseStorageStatus::all()
        ]);
    }

    public function update(Order $order, WarehouseStorage $warehouseStorage, WarehouseStoragePutRequest $request)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['receipt_date', 'document_date']);
        $data = $this->addDateToDataIfPresent($data, $request, 'receipt_date');
        $data = $this->addDateToDataIfPresent($data, $request, 'document_date');
        $warehouseStorage->update($data);
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order, 'warehouse_storage' => $warehouseStorage ])
            ->with('success', 'Se actualizó correctamente la orden de almacenamiento.');
    }

    public function updateStatus(WarehouseStorageStatusPutRequest $request, Order $order, WarehouseStorage $warehouseStorage)
    {
        $validated = $request->validated();
        if($validated["warehouse_storage_status_id"] == WarehouseStorageStatusEnum::ACTIVE && $warehouseStorage->warehouse_storage_status_id->value != WarehouseStorageStatusEnum::ACTIVE && $order->order_status_id != OrderStatusEnum::ACTIVE){
            return back()->withErrors(['Estatus' => 'Antes de reactivar el almacen primero debes reactivar la orden de servicio.']);
        }
        $warehouseStorage->update($request->validated());
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order, 'warehouse_storage' => $warehouseStorage ])
            ->with('success', 'Se actualizó correctamente el estatus la orden de almacenamiento.');
    }

    public function destroy(Order $order, WarehouseStorage $warehouseStorage): RedirectResponse
    {
        $warehouseStorage->warehouse_storage_status_id = WarehouseStorageStatusEnum::CANCELED;
        $warehouseStorage->save();
        return back()->with('success', 'La órden de almacenamiento ' . $warehouseStorage->reference . ' ha sido cancelada.');
    }

    public function notify(Order $order, WarehouseStorage $warehouseStorage): RedirectResponse
    {
        $user = $order->client->user;
        $updateMessage = "Estás recibibendo este email porque tu servicio de Almacén " . $warehouseStorage->reference . " de la orden " . $order->code . " se encuentra en estatus " . $warehouseStorage->warehouse_storage_status_id->label() . ".";
        $url = route('orders.warehouse-storages.show', [ 'order' => $order->id, 'warehouse_storage' => $warehouseStorage->id ]);
        $user->notify(new OrderUpdate($updateMessage, $url));
        return back()->with('success', 'Se ha enviado una notificación de actualización al cliente.');
    }

    private function addDateToDataIfPresent(array $data, FormRequest $request, String $dateField): array
    {
        $onlyDate = $request->safe()->only([$dateField]);
        if (!is_null($onlyDate[$dateField])) {
            $date =  Carbon::createFromFormat('d/m/Y', $onlyDate[$dateField])->format('Y-m-d');
            return array_merge($data, [ $dateField => $date ]);
        }
        return $data;
    }

    private function getOrderServices(Order $order, int $id): array
    {
        $services = [];
        foreach ($order->storages as $storage) {
            if($storage->id != $id && count($storage->products) > 0) {
                $services[] = new Service($storage, 'Almacén', 'orders.warehouse-storages.', 'warehouse_storage', $storage->warehouse_storage_status_id, $storage->created_at);
            }
        }
        foreach ($order->imports as $import) {
            if (count($import->products) > 0){
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

    public function printChecklist(Order $order, WarehouseStorage $warehouseStorage)
    {
        $documentTypes = [];
        foreach (DocumentType::where('warehouse', true)->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $warehouseStorage->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $milestones = [];
        foreach ($warehouseStorage->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->get() as $group) {
            $status = ServiceTypeStatus::findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        $data = [
            'order' => $order,
            'service' => $warehouseStorage,
            'documentTypes' => $documentTypes,
            'milestones' => $milestones
        ];
        $pdf = Pdf::loadView('order-shipment.printchecklist', $data);
        $pdf->setOptions(['dpi' => 105]);
        $pdf->setPaper('letter');
        $filename = 'CL-' . $warehouseStorage->tracking_code . '.pdf';
        return $pdf->download($filename);
    }

    public function updateChecklistComments(Request $request, Order $order, WarehouseStorage $warehouseStorage)
    {
        $validatedData = $request->validate([
            'checklist_comments' => 'required|string|max:500',
        ]);
        $warehouseStorage->update($validatedData);
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order, 'warehouse_storage' => $warehouseStorage, 'activeTab' => 3 ])
            ->with('success', 'Se actualizó correctamente el comentario del checklist.');
    }

    public function reactivate(Order $order, WarehouseStorage $warehouseStorage): RedirectResponse
    {
        if($order->order_status_id != OrderStatusEnum::ACTIVE){
            return back()->withErrors(['Estatus' => 'Antes de reactivar el almacén primero debes reactivar la orden de servicio.']);
        }
        $warehouseStorage->warehouse_storage_status_id = WarehouseStorageStatusEnum::ACTIVE;
        $warehouseStorage->save();
        return back()->with('success', 'Se ha reactivado el almacén.');
    }
}
