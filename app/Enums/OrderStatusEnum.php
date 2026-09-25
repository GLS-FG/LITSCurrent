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
            OrderStatusEnum::ACTIVE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            OrderStatusEnum::CANCELED => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 ring-red-700/10 dark:ring-red-400/20',
            OrderStatusEnum::CLOSED => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            OrderStatusEnum::ACTIVE => 'bg-green-500 dark:bg-green-400',
            OrderStatusEnum::CANCELED => 'bg-red-500 dark:bg-red-400',
            OrderStatusEnum::CLOSED => 'bg-blue-500 dark:bg-blue-400',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            OrderStatusEnum::ACTIVE => 'text-green-700 dark:text-green-400',
            OrderStatusEnum::CANCELED => 'text-red-700 dark:text-red-400',
            OrderStatusEnum::CLOSED => 'text-blue-700 dark:text-blue-400',
        };
    }
}
