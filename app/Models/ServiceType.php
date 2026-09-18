<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceType extends Model
{
    public function serviceClasses(): HasMany
    {
        return $this->hasMany(ServiceClass::class);
    }

    public function statuses(): MorphMany
    {
        return $this->morphMany(ServiceTypeStatus::class, 'serviceable')->orderBy('order_number');
    }
}
