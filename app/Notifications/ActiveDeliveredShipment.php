<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActiveDeliveredShipment extends Notification
{
    use Queueable;

    public $shipment;
    public function __construct($shipment)
    {
        $this->shipment = $shipment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'tracking_code' => $this->shipment->tracking_code,
            'order_id' => $this->shipment->order->id,
            'message' => 'El siguiente embarque se encuentra como activo pero ya ha sido entregado: '
        ];
    }
}
