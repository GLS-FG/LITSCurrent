<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassType extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'code', 'name', 'service_mode_id' ];
    public function serviceMode(): BelongsTo
    {
        return $this->belongsTo(ServiceMode::class);
    }

    public function serviceLevels(): HasMany
    {
        return $this->hasMany(ServiceLevel::class);
    }
}
