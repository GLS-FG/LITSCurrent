<?php

namespace App\Enums;

interface ServiceState
{
    public function label(): string;
    public function badgeColor(): string;
    public function dotColor(): string;
    public function textColor(): string;
}
