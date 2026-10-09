<?php

namespace App\Http\Controllers;

use App\Enums\OrderImportStatusEnum;
use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\RolesEnum;
use App\Enums\TransportationStatusEnum;
use App\Enums\WarehouseStorageStatusEnum;
use App\Http\Requests\OrderPostRequest;
use App\Http\Requests\OrderPutRequest;
use App\Http\Requests\OrderStatusPutRequest;
use App\Http\Requests\ProductsCopyPostRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderCopy;
use App\Models\OrderExport;
use App\Models\OrderImport;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\ServiceClass;
use App\Models\Warehouse;
use App\Models\WarehouseStorage;
use App\Notifications\OrderUpdate;
use App\View\Helpers\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /** Days without a registered status after which an order counts as "stale" (Dashboard panel and "My orders"). */
    public const STALE_DAYS = 7;

    public function index(Request $request)
    {
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $user = Auth::user();
        $baseQuery = Order::whereNotIn('order_status_id', [OrderStatusEnum::CLOSED, OrderStatusEnum::CANCELED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id)
            )
            ->where(function ($query) use ($code, $reference, $client, $service) {
                return $query->when($code, fn ($query, $code) => $query
                        ->where(function ($query) use ($code) {
                            return $query->where('code', 'like', '%' . $code . '%')
                                ->orWhereHas('createdBy', function ($query) use ($code) {
                                    $query
                                        ->where('name', 'like', '%' . $code . '%');
                                })
                                ->orWhereHas('shipments', function ($query) use ($code) {
                                    $query
                                        ->where('tracking_number', 'like', '%' . $code . '%')
                                        ->orWhere('tracking_code', 'like', '%' . $code . '%');
                                });
                        })
                    )
                    ->when($reference, fn ($query, $reference) => $query
                        ->where('reference', 'like', '%' . $reference . '%')
                    )
                    ->when($client, fn ($query, $client) => $query
                        ->where(function ($query) use ($client) {
                            return $query->whereHas('client', function ($query) use ($client) {
                                    $query
                                        ->where('company_name', 'like', '%' . $client . '%')
                                        ->orWhere('trade_name', 'like', '%' . $client . '%');
                                })
                                ->orWhereHas('contact', function ($query) use ($client) {
                                    $query
                                        ->where('name', 'like', '%' . $client . '%');
                                });
                        })
                    )
                    ->when($service, fn ($query, $service) => $query
                        ->where(function ($query) use ($service) {
                            return $query->whereHas('shipments', function ($query) use ($service) {
                                $query
                                    ->whereHas('serviceClass', function ($query) use ($service) {
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
                                ->orWhereHas('imports', function ($query) use ($service) {
                                    $query
                                        ->whereHas('serviceClass', function ($query) use ($service) {
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
                                ->orWhereHas('storages', function ($query) use ($service) {
                                    $query
                                        ->whereHas('serviceClass', function ($query) use ($service) {
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
                                });
                        })
                    );
            });
        $activeCount = (clone $baseQuery)->count();
        $urgentCount = (clone $baseQuery)->where('urgent', true)->count();
        $onlyUrgent = $request->boolean('urgent');
        // "My orders" is only for system users, never for clients.
        $canSeeMine = ! $user->hasRole(RolesEnum::CLIENT);
        $mineCount = $canSeeMine ? (clone $baseQuery)->where('user_id', $user->id)->count() : 0;
        $onlyMine = $canSeeMine && ! $onlyUrgent && $request->boolean('mine');
        // Only inside "My orders": hide the ones that had movement in the last STALE_DAYS days.
        $onlyStale = $onlyMine && $request->boolean('stale');
        $orders = (clone $baseQuery)
            ->when($onlyUrgent, fn ($query) => $query->where('urgent', true))
            ->when($onlyMine, fn ($query) => $query
                ->where('user_id', $user->id)
                ->withLastMovement()
                ->when($onlyStale, fn ($query) => $query->staleFor(self::STALE_DAYS))
            )
            ->when($onlyMine,
                // Longest without movement first.
                fn ($query) => $query->orderBy('last_movement_at'),
                fn ($query) => $query->orderBy('urgent', 'desc')->orderBy('created_at', 'desc')
            )
            ->paginate(20)->withQueryString();
        return view('order.index', [
            'orders' => $orders,
            'index' => route('orders.index'),
            'activeCount' => $activeCount,
            'urgentCount' => $urgentCount,
            'onlyUrgent' => $onlyUrgent,
            'canSeeMine' => $canSeeMine,
            'mineCount' => $mineCount,
            'onlyMine' => $onlyMine,
            'onlyStale' => $onlyStale,
            'staleDays' => self::STALE_DAYS,
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get(),
        ]);
    }

    public function history(Request $request)
    {
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $user = Auth::user();
        $orders = Order::whereIn('order_status_id', [OrderStatusEnum::CLOSED, OrderStatusEnum::CANCELED])
            ->when($user->hasRole(RolesEnum::CLIENT), fn ($query) => $query
                ->where('client_id', $user->client->id)
                ->where('contact_id', $user->id)
            )
            ->where(function ($query) use ($code, $reference, $client, $service) {
                return $query->when($code, fn ($query, $code) => $query
                    ->where(function ($query) use ($code) {
                        return $query->where('code', 'like', '%' . $code . '%')
                            ->orWhereHas('createdBy', function ($query) use ($code) {
                                $query
                                    ->where('name', 'like', '%' . $code . '%');
                            })
                            ->orWhereHas('shipments', function ($query) use ($code) {
                                $query
                                    ->where('tracking_number', 'like', '%' . $code . '%')
                                    ->orWhere('tracking_code', 'like', '%' . $code . '%');
                            });
                    })
                )
                    ->when($reference, fn ($query, $reference) => $query
                        ->where('reference', 'like', '%' . $reference . '%')
                    )
                    ->when($client, fn ($query, $client) => $query
                        ->where(function ($query) use ($client) {
                            return $query->whereHas('client', function ($query) use ($client) {
                                $query
                                    ->where('company_name', 'like', '%' . $client . '%')
                                    ->orWhere('trade_name', 'like', '%' . $client . '%');
                            })
                                ->orWhereHas('contact', function ($query) use ($client) {
                                    $query
                                        ->where('name', 'like', '%' . $client . '%');
                                });
                        })
                    )
                    ->when($service, fn ($query, $service) => $query
                        ->where(function ($query) use ($service) {
                            return $query->whereHas('shipments', function ($query) use ($service) {
                                $query
                                    ->whereHas('serviceClass', function ($query) use ($service) {
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
                                ->orWhereHas('imports', function ($query) use ($service) {
                                    $query
                                        ->whereHas('serviceClass', function ($query) use ($service) {
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
                                ->orWhereHas('storages', function ($query) use ($service) {
                                    $query
                                        ->whereHas('serviceClass', function ($query) use ($service) {
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
                                });
                        })
                    );
            })
            ->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();
        return view('order.index', [
            'orders' => $orders,
            'index' => route('orders.history'),
            'history' => true,
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get(),
        ]);

    }

    /**
     * Saved values of an order, used to fill the edit drawer that the list page
     * keeps mounted (the drawer is opened without loading the detail).
     */
    public function editData(Order $order)
    {
        Gate::authorize('update', $order);
        return response()->json([
            'id' => $order->id,
            'code' => $order->code,
            'update_url' => route('orders.update', ['order' => $order]),
            'client_id' => $order->client_id,
            'client_label' => $order->client->company_name . ' / ' . $order->client->trade_name,
            'contact_id' => $order->contact_id,
            'reference' => $order->reference,
            'carbon_copy' => $order->carbon_copy,
        ]);
    }

    public function create()
    {
        return view('order.create', [
            'statuses' => OrderStatus::all(),
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get()
        ]);
    }

    public function store(OrderPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $orderId = Order::whereYear('created_at', Carbon::now()->year)->count() + 1;
        $code = 'GLS-' . Carbon::now()->year . '-' . str_pad($validated['client_id'], 3, '0', STR_PAD_LEFT) . '-' . str_pad($orderId, 4, '0', STR_PAD_LEFT);
        $order = Order::create(array_merge($validated, [ 'code' => $code, 'order_status_id' => OrderStatusEnum::ACTIVE, 'user_id' => Auth::id() ]));
        return redirect()->route('orders.show', [ 'order' => $order ]);
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);

        // "Duplicar" step 2: CloneController@storeOrder sends the user here with
        // ?clone_from=<shipment id>. It is honoured only if this order was really
        // created as a copy of that shipment and the user may clone it.
        $cloneFrom = null;
        if (request()->filled('clone_from')) {
            $source = OrderShipment::find(request('clone_from'));
            if ($source && Gate::allows('clone', $source)
                && OrderCopy::where('order_shipment_id', $source->id)->where('order_id', $order->id)->exists()) {
                $cloneFrom = $source;
            }
        }

        return view('order.show', [
            'cloneFrom' => $cloneFrom,
            'order' => $order,
            'statuses' => OrderStatus::all(),
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get(),
            'services' => $this->getOrderServices($order),
            'serviceClasses' => ServiceClass::where('service_type_id', 1)->get(),
            'defaultServiceClass' => ServiceClass::where('service_type_id', 1)->first(),
            'importServiceClasses' => ServiceClass::where('service_type_id', 2)->get(),
            'defaultImportServiceClass' => ServiceClass::where('service_type_id', 2)->first(),
            'warehouses' => Warehouse::all(),
            'warehouseServiceClasses' => ServiceClass::where('service_type_id', 3)->get(),
            'defaultWarehouseServiceClass' => ServiceClass::where('service_type_id', 3)->first(),
            'instructionsOne' => "*no chain allowed, use belt/strap to secure cargo ** please make loading appointment & confirm with supplier NON‐HAZ. Included",
            'instructionsTwo' => '** Truck/Driver must meet security requirement for delivery to site*** need 24 hr alert before delivery ** can go straight to site through "Caseta de Vigilancia ** NO TAX APPLICABLE WHEN BILL TO GLS GROUP MEXICO',
        ]);
    }

    public function edit(Order $order)
    {
        return view('order.edit', [
            'order' => $order,
            'statuses' => OrderStatus::all(),
            'clients' => Client::select('id', 'name', 'last_name', 'company_name', 'trade_name')->get(),
            'contacts' => $order->client->contacts
        ]);
    }

    public function update(OrderPutRequest $request, Order $order)
    {
        $validated = $request->validated();
        if ($order->client_id != $validated['client_id']) {
            $codeParts = explode("-", $order->code);
            $codeParts[2] = str_pad($validated['client_id'], 3, '0', STR_PAD_LEFT);
            $validated['code'] = implode("-", $codeParts);
        }
        $order->update($validated);
        return redirect()->route('orders.show', [ 'order' => $order ])
            ->with('success', 'Se actualizó correctamente la orden de servicio.');
    }

    public function updateStatus(OrderStatusPutRequest $request, Order $order)
    {
        $validated = $request->validated();
        if($validated['order_status_id'] == OrderStatusEnum::CLOSED->value){
            foreach ($this->getOrderServices($order) as $service) {
                if($service->status->value == OrderStatusEnum::ACTIVE->value){
                    return back()->withErrors(['Estatus' => 'No puedes finalizar una órden cuando alguna de sus subórdenes se encuentran activas.']);
                }
            }
            $order->export_row = true;
            $order->closed_at = Carbon::now();
            $order->save();
        } else if($validated['order_status_id'] == OrderStatusEnum::CANCELED->value){
            foreach ($order->storages as $storage) {
                $storage->warehouse_storage_status_id = WarehouseStorageStatusEnum::CANCELED->value;
                $storage->save();
            }
            foreach ($order->imports as $import) {
                $import->order_import_status_id = OrderImportStatusEnum::CANCELED->value;
                $import->save();
            }
            foreach ($order->shipments as $shipment) {
                $shipment->order_shipment_status_id = OrderShipmentStatusEnum::CANCELED->value;
                $shipment->save();
                foreach ($shipment->transportations as $transportation) {
                    if($transportation->transportation_status_id->value == TransportationStatusEnum::ACTIVE->value){
                        $transportation->transportation_status_id = TransportationStatusEnum::CANCELED;
                        $transportation->save();
                    }
                }
            }
        }
        $order->update($validated);
        return redirect()->route('orders.show', [ 'order' => $order ])
            ->with('success', 'Se actualizó correctamente el estatus la orden de servicio.');
    }

    public function updateUrgent(Order $order)
    {
        $newUrgent = !$order->urgent;
        $order->update(['urgent' => $newUrgent]);
        foreach ($this->getOrderServices($order) as $service) {
            $service->service->urgent = $newUrgent;
            $service->service->save();
        }
        return redirect()->route('orders.show', [ 'order' => $order ])
            ->with('success', 'Se actualizó correctamente la orden.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->order_status_id = OrderStatusEnum::CANCELED;
        $order->save();
        return back()->with('success', 'La órden de servicio ' . $order->code . ' ha sido cancelada.');
    }

    public function notify(Order $order): RedirectResponse
    {
        $user = $order->contact;
        $updateMessage = "Estás recibibendo este email porque tu orden " . $order->code . " se encuentra en estatus " . $order->order_status_id->label() . ".";
        $url = route('orders.show', [ 'order' => $order->id ]);
        $user->notify(new OrderUpdate($updateMessage, $url));
        return back()->with('success', 'Se ha enviado una notificación de actualización al cliente.');
    }

    public function copyProducts(Order $order, ProductsCopyPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $copyFrom = null;
        $copyTo = null;
        switch ($validated['serviceable_type']) {
            case 'warehouse_storage':
                $copyFrom = WarehouseStorage::findOrFail($validated['serviceable_id']); break;
            case 'import':
                $copyFrom = OrderImport::findOrFail($validated['serviceable_id']); break;
            case 'export':
                $copyFrom = OrderExport::findOrFail($validated['serviceable_id']); break;
            case 'shipment':
                $copyFrom = OrderShipment::findOrFail($validated['serviceable_id']); break;
            default:
                abort(400, 'El tipo de servicio de copiar desde no existe.');
        }
        switch ($validated['copy_type']) {
            case 'warehouse_storage':
                $copyTo = WarehouseStorage::findOrFail($validated['copy_id']); break;
            case 'import':
                $copyTo = OrderImport::findOrFail($validated['copy_id']); break;
            case 'export':
                $copyTo = OrderExport::findOrFail($validated['copy_id']); break;
            case 'shipment':
                $copyTo = OrderShipment::findOrFail($validated['copy_id']); break;
            default:
                abort(400, 'El tipo de servicio de copiar a no existe.');
        }
        foreach ($copyFrom->products as $copyProduct) {
            $product = new OrderProduct();
            $product->order_id = $order->id;
            $product->product = $copyProduct->product;
            $product->weight = $copyProduct->weight;
            $product->container = $copyProduct->container;
            $product->quantity = $copyProduct->quantity;
            $product->value = $copyProduct->value;
            $product->reference = $copyProduct->reference;
            $product->dimensions = $copyProduct->dimensions;
            $product->height = $copyProduct->height;
            $product->width = $copyProduct->width;
            $product->length = $copyProduct->length;
            $product->unit_measure = $copyProduct->unit_measure;
            $product->weight_measure = $copyProduct->weight_measure;
            $product->incoterm_id = $copyProduct->incoterm_id;
            $copyTo->products()->save($product);
        }
        return back()->with('success', 'Los productos se copiaron correctamente.');
    }

    private function getOrderServices(Order $order): array
    {
        $services = [];
        foreach ($order->storages as $storage) {
            $services[] = new Service($storage, 'Almacén', 'orders.warehouse-storages.', 'warehouse_storage', $storage->warehouse_storage_status_id, $storage->created_at);
        }
        foreach ($order->imports as $import) {
            $services[] = new Service($import, 'Aduana', 'orders.imports.', 'import', $import->order_import_status_id, $import->created_at);
        }
        foreach ($order->exports as $export) {
            $services[] = new Service($export, 'Exportación', 'orders.exports.', 'export', $export->order_export_status_id, $export->created_at);
        }
        foreach ($order->shipments as $shipment) {
            $services[] = new Service($shipment, 'Embarque', 'orders.shipments.', 'shipment', $shipment->order_shipment_status_id, $shipment->created_at);
        }
        usort($services, function ($a, $b) {
            return $a->createdAt->timestamp - $b->createdAt->timestamp;
        });
        return $services;
    }
}
