<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceMode extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'code', 'name', 'service_class_id', 'icon' ];

    public function serviceClass(): BelongsTo
    {
        return $this->belongsTo(ServiceClass::class);
    }

    public function classTypes(): HasMany
    {
        return $this->hasMany(ClassType::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(ServiceTypeStatus::class, 'service_type_status_modes', 'service_mode_id', 'service_type_status_id')->orderBy('order_number');
    }
}
