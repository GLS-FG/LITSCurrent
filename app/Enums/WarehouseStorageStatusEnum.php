<?php

namespace App\Enums;

enum WarehouseStorageStatusEnum: int implements ServiceState
{
    case ACTIVE = 1;
    case RECEIPT = 2;
    case ISSUE = 3;
    case CANCELED = 10;
    case CLOSED = 12;
    case DELIVERED = 13;
    public function label(): string
    {
        return match ($this) {
            WarehouseStorageStatusEnum::ACTIVE => __('status.active'),
            WarehouseStorageStatusEnum::CANCELED => __('status.cancelled'),
            WarehouseStorageStatusEnum::CLOSED => __('status.closed'),
            WarehouseStorageStatusEnum::RECEIPT => 'Entrada Almacen',
            WarehouseStorageStatusEnum::ISSUE => 'Salida almacén',
            WarehouseStorageStatusEnum::DELIVERED => 'Entregado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            WarehouseStorageStatusEnum::ACTIVE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            WarehouseStorageStatusEnum::RECEIPT => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            WarehouseStorageStatusEnum::ISSUE => 'bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 ring-yellow-700/10 dark:ring-yellow-400/20',
            WarehouseStorageStatusEnum::CANCELED => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 ring-red-700/10 dark:ring-red-400/20',
            WarehouseStorageStatusEnum::CLOSED => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
            WarehouseStorageStatusEnum::DELIVERED => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            WarehouseStorageStatusEnum::ACTIVE, WarehouseStorageStatusEnum::DELIVERED => 'bg-green-500 dark:bg-green-400',
            WarehouseStorageStatusEnum::RECEIPT => 'bg-cyan-500 dark:bg-cyan-400',
            WarehouseStorageStatusEnum::ISSUE => 'bg-yellow-500 dark:bg-yellow-400',
            WarehouseStorageStatusEnum::CANCELED => 'bg-red-500 dark:bg-red-400',
            WarehouseStorageStatusEnum::CLOSED => 'bg-blue-500 dark:bg-blue-400',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            WarehouseStorageStatusEnum::ACTIVE, WarehouseStorageStatusEnum::DELIVERED => 'text-green-700 dark:text-green-400',
            WarehouseStorageStatusEnum::RECEIPT => 'text-cyan-700 dark:text-cyan-400',
            WarehouseStorageStatusEnum::ISSUE => 'text-yellow-700 dark:text-yellow-400',
            WarehouseStorageStatusEnum::CANCELED => 'text-red-700 dark:text-red-400',
            WarehouseStorageStatusEnum::CLOSED => 'text-blue-700 dark:text-blue-400',
        };
    }
}
