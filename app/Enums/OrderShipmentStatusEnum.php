<?php

namespace App\Enums;

enum OrderShipmentStatusEnum: int implements ServiceState
{
    case ACTIVE = 1;
    case CLEARANCE = 4;
    case CANCELED = 10;
    case TRANSIT = 11;
    case CLOSED = 12;
    case DELIVERED = 13;
    public function label(): string
    {
        return match ($this) {
            OrderShipmentStatusEnum::ACTIVE => __('status.active'),
            OrderShipmentStatusEnum::CANCELED => __('status.cancelled'),
            OrderShipmentStatusEnum::CLOSED => __('status.closed'),
            OrderShipmentStatusEnum::CLEARANCE => 'Desaduanado',
            OrderShipmentStatusEnum::TRANSIT => 'En transito',
            OrderShipmentStatusEnum::DELIVERED => 'Entregado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            OrderShipmentStatusEnum::ACTIVE => 'bg-green-50 text-green-700 ring-green-700/10',
            OrderShipmentStatusEnum::CLEARANCE => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            OrderShipmentStatusEnum::CANCELED => 'bg-red-50 text-red-700 ring-red-700/10',
            OrderShipmentStatusEnum::TRANSIT => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            OrderShipmentStatusEnum::CLOSED => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            OrderShipmentStatusEnum::DELIVERED => 'bg-green-50 text-green-700 ring-green-700/10',
        };
    }
}
