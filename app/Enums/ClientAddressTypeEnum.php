<?php

namespace App\Enums;

enum ClientAddressTypeEnum: int
{
    case BILLING = 0;
    case SHIPPING = 1;
    public function label(): string
    {
        return match ($this) {
            ClientAddressTypeEnum::BILLING => 'Dirección de facturación',
            ClientAddressTypeEnum::SHIPPING => 'Dirección de entrega'
        };
    }
}
