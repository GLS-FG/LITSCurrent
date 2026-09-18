<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLocation extends Model
{
    use SoftDeletes;
    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServiceTypeStatus::class, 'service_type_status_id', 'id')->withTrashed();
    }

    protected function casts(): array
    {
        return [
            'location_date' => 'datetime:Y-m-d H:i:s'
        ];
    }
}
