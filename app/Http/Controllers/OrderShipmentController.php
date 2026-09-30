<?php

namespace App\Http\Controllers;

use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\TransportationStatusEnum;
use App\Http\Requests\OrderProductPostRequest;
use App\Http\Requests\OrderShipmentPostRequest;
use App\Http\Requests\OrderShipmentPutRequest;
use App\Http\Requests\OrderShipmentStatusPutRequest;
use App\Models\Client;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\DocumentType;
use App\Models\GeolocationStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\OrderShipmentStatus;
use App\Models\PetitionCode;
use App\Models\ServiceClass;
use App\Models\ServiceTypeStatus;
use App\Models\TransportationStatus;
use App\Notifications\OrderShipmentUpdate;
use App\View\Helpers\OrderDocumentCount;
use App\View\Helpers\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class OrderShipmentController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $status = $request->get('status_search');
        $user = Auth::user();
        $baseQuery = OrderShipment::whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CLOSED, OrderShipmentStatusEnum::CANCELED])
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
                            ->orWhere('tracking_number', 'like', '%' . $code . '%')
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
            });
        $activeCount = (clone $baseQuery)->count();
        $urgentCount = (clone $baseQuery)->where('urgent', true)->count();
        $onlyUrgent = $request->boolean('urgent');
        $shipments = (clone $baseQuery)
            ->when($onlyUrgent, fn ($query) => $query->where('urgent', true))
            ->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();
        return view('order-shipment.index', [
            'shipments' => $shipments,
            'statuses' => OrderShipmentStatus::all(),
            'activeCount' => $activeCount,
            'urgentCount' => $urgentCount,
            'onlyUrgent' => $onlyUrgent,
            'serviceClasses' => ServiceClass::where('service_type_id', 1)->get(),
            'defaultServiceClass' => ServiceClass::where('service_type_id', 1)->first(),
        ]);
    }

    /**
     * Saved values of a shipment, used to fill the edit drawer that the list
     * page keeps mounted (the drawer is opened without loading the detail).
     */
    public function editData(Order $order, OrderShipment $shipment)
    {
        abort_unless($shipment->order_id === $order->id, 404);
        Gate::authorize('update', $shipment);
        return response()->json([
            'id' => $shipment->id,
            'order_id' => $order->id,
            'order_code' => $order->code,
            'client' => $order->client->trade_name,
            'update_url' => route('orders.shipments.update', ['order' => $order, 'shipment' => $shipment]),
            'reference' => $shipment->reference,
            'origin_country_id' => $shipment->origin_country_id,
            'origin_state_id' => $shipment->origin_state_id,
            'origin_city_id' => $shipment->origin_city_id,
            'ship_from_name' => $shipment->ship_from_name,
            'ship_from_id' => $shipment->ship_from_id,
            'ship_from' => $shipment->ship_from,
            'ship_from_link' => $shipment->ship_from_link,
            'estimated_time_departure' => $shipment->estimated_time_departure?->format('d/m/Y'),
            'destination_country_id' => $shipment->destination_country_id,
            'destination_state_id' => $shipment->destination_state_id,
            'destination_city_id' => $shipment->destination_city_id,
            'ship_to_name' => $shipment->ship_to_name,
            'ship_to_id' => $shipment->ship_to_id,
            'ship_to' => $shipment->ship_to,
            'ship_to_link' => $shipment->ship_to_link,
            'estimated_time_arrival' => $shipment->estimated_time_arrival?->format('d/m/Y'),
            'instructions1' => $shipment->instructions1,
            'instructions2' => $shipment->instructions2,
            'comments' => $shipment->comments,
            'oversize' => $shipment->oversize,
            'hazardous_material' => $shipment->hazardous_material,
            'refrigerated' => $shipment->refrigerated,
            'insurance' => $shipment->insurance,
            'tarps' => $shipment->tarps,
            'service_class_id' => $shipment->service_class_id,
            'service_mode_id' => $shipment->service_mode_id,
            'class_type_id' => $shipment->class_type_id,
            'service_level_id' => $shipment->service_level_id,
        ]);
    }

    public function create(Order $order)
    {
        $instructionsOne = "*no chain allowed, use belt/strap to secure cargo ** please make loading appointment & confirm with supplier NON‐HAZ. Included";
        $instructionsTwo = '** Truck/Driver must meet security requirement for delivery to site*** need 24 hr alert before delivery ** can go straight to site through "Caseta de Vigilancia ** NO TAX APPLICABLE WHEN BILL TO GLS GROUP MEXICO';
        $serviceClasses = ServiceClass::where('service_type_id', 1)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 1)->first();
        return view('order-shipment.create', [
            'order' => $order,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
            'statuses' => OrderShipmentStatus::all(),
            'instructionsOne' => $instructionsOne,
            'instructionsTwo' => $instructionsTwo,
        ]);
    }

    public function store(Order $order, OrderShipmentPostRequest $request)
    {
        $validated = $request->validated();
        $validated = $request->safe()->except(['estimated_time_departure', 'estimated_time_arrival']);
        $orderId = OrderShipment::count() + 1;
        $trackingCode = 'GLS' . str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT) . '02' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $trackingNumber = 'GLS' . Carbon::now()->timestamp;
        $data = array_merge($validated, [ 'order_id' => $order->id, 'tracking_code' => $trackingCode, 'order_shipment_status_id' => OrderShipmentStatusEnum::ACTIVE, 'tracking_number' => $trackingNumber, 'urgent' => $order->urgent ]);
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_departure');
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_arrival');
        $shipment = OrderShipment::create($data);
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ]);
    }

    public function storeProduct(Order $order, OrderShipment $shipment, OrderProductPostRequest $request)
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
        $shipment->products()->save($product);
        return back()->with('success', 'La mercancía ha sido creada.');
    }

    public function storeDriver(Order $order, OrderShipment $shipment)
    {
        $expiresAt = Carbon::now()->addMinutes(30);
        $driverLink = URL::temporarySignedRoute('orders.shipments.drivers.store', $expiresAt, ['order' => $order->id, 'shipment' => $shipment->id]);
        $shipment->driver_link = $driverLink;
        $shipment->driver_link_expires_at = $expiresAt;
        $shipment->driver_link_used = false;
        $shipment->save();
        return back()->with('success', 'Se ha creado la liga para compartir al chofer, revísala en Expediente.');
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
            if (count($export->products) > 0){
                $services[] = new Service($export, 'Exportación', 'orders.exports.', 'export', $export->order_export_status_id, $export->created_at);
            }
        }
        foreach ($order->shipments as $shipment) {
            if($shipment->id != $id && count($shipment->products) > 0) {
                $services[] = new Service($shipment, 'Embarque', 'orders.shipments.', 'shipment', $shipment->order_shipment_status_id, $shipment->created_at);
            }
        }
        usort($services, function ($a, $b) {
            return $a->createdAt->timestamp - $b->createdAt->timestamp;
        });
        return $services;
    }

    public function show(Order $order, OrderShipment $shipment)
    {
        Gate::authorize('view', $shipment);
        $documentTypes = [];
        foreach (DocumentType::where('shipments', true)->orderBy('order_number')->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $shipment->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $now = Carbon::now();
        $whatsapp = "";
        if($shipment->latestLocation != null) {
            $status = $shipment->latestLocation->status->name;
            $locationName = "";
            if($shipment->latestLocation->name != null){
                $locationName = ' (' . $shipment->latestLocation->name . ')';
            }
            $updateDate = $shipment->latestLocation->location_date->format('d/m/Y h:i A');
            $whatsapp = "Su embarque de *" . $shipment->ship_from_name . "* " . $shipment->originCity->name . ", " . $shipment->originState->name . " hacia *" . $shipment->ship_to_name . "* " . $shipment->destinationCity->name . ", " . $shipment->destinationState->name . " con num. de rastreo *" . $shipment->tracking_number . "* cuenta con estatus *" . $status . "*" . $locationName . " el dia de hoy " . $updateDate . '. ' . $shipment->latestLocation->comments;
        }
        $milestones = [];
        foreach ($shipment->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->orderBy('location_date')->get() as $group) {
            $status = ServiceTypeStatus::withTrashed()->findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        $mapAvailable = false;
        $mapLocations = [];
        foreach ($shipment->locations as $location) {
            if($location->latitude != null && $location->longitude != null) {
                $mapAvailable = true;
                $mapLocations[] = $location;
            }
        }
        return view('order-shipment.show', [
            'order' => $order,
            'shipment' => $shipment,
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get(),
            'now' => $now,
            'milestones' => $milestones,
            'whatsapp' => urlencode($whatsapp),
            'geolocationStatuses' => GeolocationStatus::all(),
            'statuses' => OrderShipmentStatus::all(),
            'documentTypes' => $documentTypes,
            'transportationStatuses' => TransportationStatus::all(),
            'mapAvailable' => $mapAvailable,
            'mapLocations' => $mapLocations,
            'serviceClasses' => ServiceClass::where('service_type_id', 1)->get(),
            'defaultServiceClass' => ServiceClass::where('service_type_id', 1)->first(),
        ]);
    }

    public function edit(Order $order, OrderShipment $shipment)
    {
        $serviceClasses = ServiceClass::where('service_type_id', 1)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 1)->first();
        return view('order-shipment.edit', [
            'order' => $order,
            'shipment' => $shipment,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'statuses' => OrderShipmentStatus::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
        ]);
    }

    public function update(Order $order, OrderShipment $shipment, OrderShipmentPutRequest $request)
    {
        $validated = $request->validated();
        $data = $request->safe()->except(['estimated_time_departure', 'estimated_time_arrival']);
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_departure');
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_arrival');
        $shipment->update($data);
        return redirect()->route('orders.shipments.show', [ 'order' => $order, 'shipment' => $shipment ])
            ->with('success', 'Se actualizó correctamente la orden de embarque.');
    }

    public function updateStatus(OrderShipmentStatusPutRequest $request, Order $order, OrderShipment $shipment)
    {
        $validated = $request->validated();
        if($validated['order_shipment_status_id'] == OrderShipmentStatusEnum::CLOSED->value){
            if($shipment->start_date == null){
                return back()->withErrors(['Estatus' => 'No puedes finalizar un embarque cuando no se ha ingresado su ATD, ve a la pestaña de "Estatus" e ingresa la etapa de "Picked up".']);
            }
            if($shipment->end_date == null){
                return back()->withErrors(['Estatus' => 'No puedes finalizar un embarque cuando no se ha ingresado su ATA, ve a la pestaña de "Estatus" e ingresa la etapa de "Delivered".']);
            }
            foreach ($shipment->transportations as $transportation) {
                $transportation->transportation_status_id = TransportationStatusEnum::CLOSED;
                $transportation->save();
            }
        } else if($validated['order_shipment_status_id'] == OrderShipmentStatusEnum::CANCELED->value){
            foreach ($shipment->transportations as $transportation) {
                if($transportation->transportation_status_id->value == TransportationStatusEnum::ACTIVE->value){
                    $transportation->transportation_status_id = TransportationStatusEnum::CANCELED;
                    $transportation->save();
                }
            }
        } else if($validated['order_shipment_status_id'] == OrderShipmentStatusEnum::ACTIVE->value && $shipment->order_shipment_status_id->value != OrderShipmentStatusEnum::ACTIVE && $order->order_status_id != OrderStatusEnum::ACTIVE){
            return back()->withErrors(['Estatus' => 'Antes de reactivar el embarque primero debes reactivar la orden de servicio.']);
        }
        $shipment->update($validated);
        return redirect()->route('orders.shipments.show', [ 'order' => $order, 'shipment' => $shipment ])
            ->with('success', 'Se actualizó correctamente el estatus la orden de embarque.');
    }

    public function destroy(Order $order, OrderShipment $shipment): RedirectResponse
    {
        $shipment->order_shipment_status_id = OrderShipmentStatusEnum::CANCELED;
        $shipment->save();
        return back()->with('success', 'La órden de embarque ' . $shipment->reference . ' ha sido cancelada.');
    }

    public function notify(Order $order, OrderShipment $shipment): RedirectResponse
    {
        $user = $order->contact;
        $reference = $order->code;
        $status = $shipment->latestLocation->status->name;
        $locationName = "";
        if($shipment->latestLocation->name != null){
            $locationName = $status . ' (' . $shipment->latestLocation->name . ')';
        }
        $updateDate = $shipment->latestLocation->location_date->format('d/m/Y h:i A');
        $updateMessage = "Su embarque de **" . $shipment->ship_from_name . "** " . $shipment->originCity->name . ", " . $shipment->originState->name . " hacia **" . $shipment->ship_to_name . "** " . $shipment->destinationCity->name . ", " . $shipment->destinationState->name . " con num. de rastreo **" . $shipment->tracking_number . '** cuenta con estatus:';
        $updateComment = $shipment->latestLocation->comments;
        $updateETA = "";
        if ($shipment->latestLocation->status->id != 20 && $shipment->estimated_time_arrival != null) {
            $updateETA = "ETA: " . $shipment->estimated_time_arrival->format('d/m/Y');
        }
        $url = route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ]);
        $user->notify(new OrderShipmentUpdate($reference, $status, $updateDate,  $updateMessage, $updateComment, $updateETA, $locationName, $url));
        return back()->with('success', 'Se ha enviado una notificación de actualización al cliente.');
    }

    public function showBOL(Order $order, OrderShipment $shipment)
    {
        return view('order-shipment.billoflanding', [
            'order' => $order,
            'shipment' => $shipment,
            'products' => $order->products,
            'transportation' => $shipment->transportation,
        ]);
    }

    public function printBOL(Order $order, OrderShipment $shipment)
    {
        $data = [
            'order' => $order,
            'shipment' => $shipment
        ];
        $pdf = Pdf::loadView('order-shipment.printbill', $data);
        $pdf->setOptions(['dpi' => 105]);
        $pdf->setPaper('letter');
        $filename = 'BL-' . $shipment->tracking_number . '.pdf';
        return $pdf->download($filename);
    }

    public function printChecklist(Order $order, OrderShipment $shipment)
    {
        $documentTypes = [];
        foreach (DocumentType::where('shipments', true)->get() as $documentType) {
            $item = new OrderDocumentCount($documentType->name, $shipment->documents()->where('document_type_id', $documentType->id)->count(), $documentType->is_required);
            $documentTypes[] = $item;
        }
        $milestones = [];
        foreach ($shipment->locations()->selectRaw('min(created_at) as location_date, service_type_status_id')->groupBy('service_type_status_id')->get() as $group) {
            $status = ServiceTypeStatus::withTrashed()->findOrFail($group->service_type_status_id);
            $milestones[] = ['status' => $status->name, 'date' => $group->location_date];
        }
        $data = [
            'order' => $order,
            'service' => $shipment,
            'documentTypes' => $documentTypes,
            'milestones' => $milestones
        ];
        $pdf = Pdf::loadView('order-shipment.printchecklist', $data);
        $pdf->setOptions(['dpi' => 105]);
        $pdf->setPaper('letter');
        $filename = 'CL-' . $shipment->tracking_number . '.pdf';
        return $pdf->download($filename);
    }

    public function previewNotify(Order $order, OrderShipment $shipment)
    {
        $user = $order->contact;
        $reference = $order->code;
        $status = $shipment->latestLocation->status->name;
        $locationName = "";
        if($shipment->latestLocation->name != null){
            $locationName = $shipment->latestLocation->name;
        }
        $updateDate = $shipment->latestLocation->location_date->format('d/m/Y h:i A');
        $updateMessage = "Su embarque de **" . $shipment->ship_from_name . "** " . $shipment->originCity->name . ", " . $shipment->originState->name . " hacia **" . $shipment->ship_to_name . "** " . $shipment->destinationCity->name . ", " . $shipment->destinationState->name . " con num. de rastreo **" . $shipment->tracking_number . '** cuenta con estatus:';
        $updateComment = $shipment->latestLocation->comments;
        $updateETA = "";
        if ($shipment->latestLocation->status->id != 20 && $shipment->estimated_time_arrival != null){
            $updateETA = "ETA: " . $shipment->estimated_time_arrival->format('d/m/Y');
        }
        $url = route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ]);
        return (new OrderShipmentUpdate($reference, $status, $updateDate,  $updateMessage, $updateComment, $updateETA, $locationName, $url))->toMail($user)->render();
    }

    public function updateChecklistComments(Request $request, Order $order, OrderShipment $shipment)
    {
        $validatedData = $request->validate([
            'checklist_comments' => 'required|string',
        ]);
        $shipment->update($validatedData);
        return redirect()->route('orders.shipments.show', [ 'order' => $order, 'shipment' => $shipment, 'activeTab' => 3 ])
            ->with('success', 'Se actualizó correctamente el comentario del checklist.');
    }

    public function updateMap(Request $request, Order $order, OrderShipment $shipment)
    {
        $showMap = $request->boolean('show_map');
        $shipment->show_map = $showMap;
        $shipment->save();
        return response()->json(['message' => 'El valor del mapa se actualizaó correctamente'], 200);
    }
}
