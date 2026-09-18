<?php

namespace App\View\Helpers;

class NotificationEvent
{
    public int $id;
    public string $clientName;
    public string $clientImage;
    public string $contactName;
    public string $orderCode;
    public string $orderId;
    public string $trackingNumber;
    public int $pending;
    public array $events;
}
