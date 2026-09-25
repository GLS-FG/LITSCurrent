<?php

namespace App\Enums;

enum OrderImportStatusEnum: int implements ServiceState
{

    case ACTIVE = 1;
    case CLEARANCE = 4;
    case CANCELED = 10;
    case CLOSED = 12;
    case DELIVERED = 13;
    public function label(): string
    {
        return match ($this) {
            OrderImportStatusEnum::ACTIVE => __('status.active'),
            OrderImportStatusEnum::CANCELED => __('status.cancelled'),
            OrderImportStatusEnum::CLOSED => __('status.closed'),
            OrderImportStatusEnum::CLEARANCE => 'Desaduanado',
            OrderImportStatusEnum::DELIVERED => 'Entregado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            OrderImportStatusEnum::ACTIVE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            OrderImportStatusEnum::CLEARANCE => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            OrderImportStatusEnum::CANCELED => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 ring-red-700/10 dark:ring-red-400/20',
            OrderImportStatusEnum::CLOSED => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
            OrderImportStatusEnum::DELIVERED => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            OrderImportStatusEnum::ACTIVE, OrderImportStatusEnum::DELIVERED => 'bg-green-500 dark:bg-green-400',
            OrderImportStatusEnum::CLEARANCE => 'bg-cyan-500 dark:bg-cyan-400',
            OrderImportStatusEnum::CANCELED => 'bg-red-500 dark:bg-red-400',
            OrderImportStatusEnum::CLOSED => 'bg-blue-500 dark:bg-blue-400',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            OrderImportStatusEnum::ACTIVE, OrderImportStatusEnum::DELIVERED => 'text-green-700 dark:text-green-400',
            OrderImportStatusEnum::CLEARANCE => 'text-cyan-700 dark:text-cyan-400',
            OrderImportStatusEnum::CANCELED => 'text-red-700 dark:text-red-400',
            OrderImportStatusEnum::CLOSED => 'text-blue-700 dark:text-blue-400',
        };
    }
}
