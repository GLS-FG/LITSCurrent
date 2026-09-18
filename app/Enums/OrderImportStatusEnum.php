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
            OrderImportStatusEnum::ACTIVE => 'bg-green-50 text-green-700 ring-green-700/10',
            OrderImportStatusEnum::CLEARANCE => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            OrderImportStatusEnum::CANCELED => 'bg-red-50 text-red-700 ring-red-700/10',
            OrderImportStatusEnum::CLOSED => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            OrderImportStatusEnum::DELIVERED => 'bg-green-50 text-green-700 ring-green-700/10',
        };
    }
}
