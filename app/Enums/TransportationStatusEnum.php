<?php

namespace App\Enums;

enum TransportationStatusEnum: int implements ServiceState
{

    case ACTIVE = 1;
    case LOADED = 7;
    case REGISTERED = 8;
    case DISPATCHED = 9;
    case CANCELED = 10;
    case TRANSIT = 11;
    case CLOSED = 12;
    public function label(): string
    {
        return match ($this) {
            TransportationStatusEnum::ACTIVE => 'Activo',
            TransportationStatusEnum::LOADED => 'Cargado',
            TransportationStatusEnum::REGISTERED => 'Registrado',
            TransportationStatusEnum::DISPATCHED => 'Despachado',
            TransportationStatusEnum::CANCELED => 'Cancelado',
            TransportationStatusEnum::TRANSIT => 'En transito',
            TransportationStatusEnum::CLOSED => 'Finalizado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            TransportationStatusEnum::ACTIVE => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            TransportationStatusEnum::LOADED => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            TransportationStatusEnum::REGISTERED => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            TransportationStatusEnum::DISPATCHED => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 ring-cyan-700/10 dark:ring-cyan-400/20',
            TransportationStatusEnum::CANCELED => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 ring-red-700/10 dark:ring-red-400/20',
            TransportationStatusEnum::TRANSIT => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-700/10 dark:ring-green-400/20',
            TransportationStatusEnum::CLOSED => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-blue-700/10 dark:ring-blue-400/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            TransportationStatusEnum::ACTIVE, TransportationStatusEnum::TRANSIT => 'bg-green-500 dark:bg-green-400',
            TransportationStatusEnum::LOADED, TransportationStatusEnum::REGISTERED, TransportationStatusEnum::DISPATCHED => 'bg-cyan-500 dark:bg-cyan-400',
            TransportationStatusEnum::CANCELED => 'bg-red-500 dark:bg-red-400',
            TransportationStatusEnum::CLOSED => 'bg-blue-500 dark:bg-blue-400',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            TransportationStatusEnum::ACTIVE, TransportationStatusEnum::TRANSIT => 'text-green-700 dark:text-green-400',
            TransportationStatusEnum::LOADED, TransportationStatusEnum::REGISTERED, TransportationStatusEnum::DISPATCHED => 'text-cyan-700 dark:text-cyan-400',
            TransportationStatusEnum::CANCELED => 'text-red-700 dark:text-red-400',
            TransportationStatusEnum::CLOSED => 'text-blue-700 dark:text-blue-400',
        };
    }
}
