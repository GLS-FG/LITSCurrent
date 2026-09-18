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
            TransportationStatusEnum::ACTIVE => 'bg-green-50 text-green-700 ring-green-700/10',
            TransportationStatusEnum::LOADED => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            TransportationStatusEnum::REGISTERED => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            TransportationStatusEnum::DISPATCHED => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
            TransportationStatusEnum::CANCELED => 'bg-red-50 text-red-700 ring-red-700/10',
            TransportationStatusEnum::TRANSIT => 'bg-green-50 text-green-700 ring-green-700/10',
            TransportationStatusEnum::CLOSED => 'bg-blue-50 text-blue-700 ring-blue-700/10',
        };
    }
}
