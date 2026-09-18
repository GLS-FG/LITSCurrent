<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTypeStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'order_number', 'color'
    ];
    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function modes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceMode::class, 'service_type_status_modes', 'service_type_status_id', 'service_mode_id');
    }
}
