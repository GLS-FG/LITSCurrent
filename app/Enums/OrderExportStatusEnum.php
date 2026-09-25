<?php

namespace App\Enums;


enum OrderExportStatusEnum: int implements ServiceState
{
    case ACTIVE = 1;
    case CLEARANCE = 4;
    case CANCELED = 10;
    case CLOSED = 12;
    case DELIVERED = 13;
    public function label(): string
    {
        return match ($this) {
            OrderExportStatusEnum::ACTIVE => 'Activo',
            OrderExportStatusEnum::CLEARANCE => 'Desaduanado',
            OrderExportStatusEnum::CANCELED => 'Cancelado',
            OrderExportStatusEnum::CLOSED => 'Finalizado',
            OrderExportStatusEnum::DELIVERED => 'Entregado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            OrderExportStatusEnum::ACTIVE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            OrderExportStatusEnum::CLEARANCE => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            OrderExportStatusEnum::CANCELED => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 ring-red-700/10 dark:ring-red-400/20',
            OrderExportStatusEnum::CLOSED => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
            OrderExportStatusEnum::DELIVERED => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            OrderExportStatusEnum::ACTIVE, OrderExportStatusEnum::DELIVERED => 'bg-green-500 dark:bg-green-400',
            OrderExportStatusEnum::CLEARANCE => 'bg-cyan-500 dark:bg-cyan-400',
            OrderExportStatusEnum::CANCELED => 'bg-red-500 dark:bg-red-400',
            OrderExportStatusEnum::CLOSED => 'bg-blue-500 dark:bg-blue-400',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            OrderExportStatusEnum::ACTIVE, OrderExportStatusEnum::DELIVERED => 'text-green-700 dark:text-green-400',
            OrderExportStatusEnum::CLEARANCE => 'text-cyan-700 dark:text-cyan-400',
            OrderExportStatusEnum::CANCELED => 'text-red-700 dark:text-red-400',
            OrderExportStatusEnum::CLOSED => 'text-blue-700 dark:text-blue-400',
        };
    }
}
