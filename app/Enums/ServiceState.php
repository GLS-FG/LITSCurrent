<?php

namespace App\Enums;

interface ServiceState
{
    public function label(): string;
    public function badgeColor(): string;
}
