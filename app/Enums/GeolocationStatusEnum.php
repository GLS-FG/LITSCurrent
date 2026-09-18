<?php

namespace App\Enums;

enum GeolocationStatusEnum: int
{
    case PICKEDUP = 1;
    case DELIVERED = 20;
    case ATORIGIN = 21;
    case OVERAGE = 23;
    case ATDESTINATION = 37;
    public function label(): string
    {
        return match ($this) {
            GeolocationStatusEnum::PICKEDUP => 'Picked up',
            GeolocationStatusEnum::DELIVERED => 'Delivered',
            GeolocationStatusEnum::ATORIGIN => 'At origin',
            GeolocationStatusEnum::OVERAGE => 'Overage',
            GeolocationStatusEnum::ATDESTINATION => 'At destination',
        };
    }
}
