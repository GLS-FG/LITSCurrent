<?php

namespace App\Http\Controllers;

use App\Enums\OrderShipmentStatusEnum;
use App\Enums\RolesEnum;
use App\Models\Notification;
use App\Models\OrderShipment;
use App\View\Helpers\NotificationCenterService;
use App\View\Helpers\NotificationEvent;
use App\View\Helpers\NotificationEventItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use function Psy\debug;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Notification::class);
        $notifications = [];
        $code = $request->get('code_search');
        $reference = $request->get('reference_search');
        $client = $request->get('client_search');
        $service = $request->get('service_search');
        $status = $request->get('status_search');
        $shipments = OrderShipment::whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CLOSED, OrderShipmentStatusEnum::CANCELED])
            ->where(function ($query) use ($code, $reference, $client, $service, $status) {
                return $query->when($code, fn ($query, $code) => $query
                    ->where(function ($query) use ($code) {
                        return $query->where('tracking_code', 'like', '%' . $code . '%')
                            ->orWhere('tracking_number', 'like', '%' . $code . '%')
                            ->orWhereHas('order', function ($query) use ($code) {
                                return $query->where('code', 'like', '%' . $code . '%')
                                ->orWhereHas('createdBy', function ($query) use ($code) {
                                    $query
                                        ->where('name', 'like', '%' . $code . '%');
                                });
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
            ->get();

        foreach ($shipments as $shipment) {
            $notification = new NotificationCenterService();
            $notification->id = $shipment->id;
            $notification->service = $shipment;
            $events = [];
            $pendingEvents = 0;
            $today = Carbon::now();
            $yesterday = Carbon::now()->subDay();
            $pickedUpShipment = $shipment->locations()->where('service_type_status_id', 1)->get();
            $deliveredShipment = $shipment->locations()->where('service_type_status_id', 20)->get();
            $notUpdatedShipment = $shipment->locations()->where('created_at', '>', $yesterday)->get();
            if(count($pickedUpShipment) <= 0 && $today->greaterThanOrEqualTo($shipment->estimated_time_departure)) {
                $pickedUpEvent = new NotificationEventItem("Recolección pendiente", false);
                $pendingEvents += 1;
                $events[] = $pickedUpEvent;
            }
            if(count($pickedUpShipment) > 1) {
                $notUpdatedEvent = new NotificationEventItem("Estatus no actualizado 24hrs", false);
                if(count($notUpdatedShipment) <= 0) {
                    $pendingEvents += 1;
                    $events[] = $notUpdatedEvent;
                }
            }
            if(count($deliveredShipment) <= 0 && $today->greaterThanOrEqualTo($shipment->estimated_time_arrival)) {
                $deliveredEvent = new NotificationEventItem("Entrega pendiente", false);
                $pendingEvents += 1;
                $events[] = $deliveredEvent;
            }
            if(count($deliveredShipment) > 0) {
                $activeDeliveredEvent = new NotificationEventItem("Embarque entregado activo", false);
                $pendingEvents += 1;
                $events[] = $activeDeliveredEvent;
            }
            $notification->pending = $pendingEvents;
            $notification->events = $events;
            if (count($events) > 0){
                $notifications[] = $notification;
            }
        }
        // Same Activas / Urgentes tabs as the other lists: the counts are taken
        // before the urgent filter so both tabs always show their own total.
        $activeCount = count($notifications);
        $urgentCount = count(array_filter($notifications, fn ($notification) => $notification->service->urgent));
        $onlyUrgent = $request->boolean('urgent');
        if ($onlyUrgent) {
            $notifications = array_values(array_filter($notifications, fn ($notification) => $notification->service->urgent));
        }
        return view('notification.index', [
            'notifications' => $notifications,
            'activeCount' => $activeCount,
            'urgentCount' => $urgentCount,
            'onlyUrgent' => $onlyUrgent,
        ]);
    }
}
