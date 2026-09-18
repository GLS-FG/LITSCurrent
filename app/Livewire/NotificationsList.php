<?php

namespace App\Livewire;

use App\Enums\OrderShipmentStatusEnum;
use App\Models\OrderShipment;
use App\View\Helpers\NotificationEvent;
use App\View\Helpers\NotificationEventItem;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Carbon;

class NotificationsList extends Component
{
    public function render()
    {
        $notifications = [];
        $user = auth()->user();
        $shipments = OrderShipment::whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CLOSED, OrderShipmentStatusEnum::CANCELED])
            ->whereHas('order', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($shipments as $shipment) {
            $notification = new NotificationEvent();
            $notification->id = $shipment->id;
            $notification->clientName = $shipment->order->client->trade_name;
            $notification->clientImage = $shipment->order->client->image;
            $notification->contactName = $shipment->reference;
            $notification->orderId = $shipment->order->id;
            $notification->orderCode = $shipment->order->code;
            $notification->trackingNumber = $shipment->tracking_number;
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
        return view('livewire.notifications-list', [
            "notifications" => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        auth()->user()->refresh();
    }
}
