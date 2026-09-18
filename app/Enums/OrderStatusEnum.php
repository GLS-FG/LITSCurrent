<?php

namespace App\Enums;

enum OrderStatusEnum: int implements ServiceState
{
    case ACTIVE = 1;
    case CANCELED = 10;
    case CLOSED = 12;
    public function label(): string
    {
        return match ($this) {
            OrderStatusEnum::ACTIVE => __('status.active'),
            OrderStatusEnum::CANCELED => __('status.cancelled'),
            OrderStatusEnum::CLOSED => __('status.closed'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            OrderStatusEnum::ACTIVE => 'bg-green-50 text-green-700 ring-green-700/10',
            OrderStatusEnum::CANCELED => 'bg-red-50 text-red-700 ring-red-700/10',
            OrderStatusEnum::CLOSED => 'bg-blue-50 text-blue-700 ring-blue-700/10',
        };
    }
}
