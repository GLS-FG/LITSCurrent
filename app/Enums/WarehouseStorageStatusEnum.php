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
            WarehouseStorageStatusEnum::ACTIVE => 'bg-green-50 text-green-700 ring-green-700/10',
            WarehouseStorageStatusEnum::RECEIPT => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            WarehouseStorageStatusEnum::ISSUE => 'bg-yellow-50 text-yellow-700 ring-yellow-700/10',
            WarehouseStorageStatusEnum::CANCELED => 'bg-red-50 text-red-700 ring-red-700/10',
            WarehouseStorageStatusEnum::CLOSED => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            WarehouseStorageStatusEnum::DELIVERED => 'bg-green-50 text-green-700 ring-green-700/10',
        };
    }
}
