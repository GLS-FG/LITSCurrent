<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Geolocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'latitude','longitude','location_date','comments'
    ];

    protected function casts(): array
    {
        return [
            'location_date' => 'datetime:Y-m-d H:i:s'
        ];
    }

    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }

    public function shipmentLocation(): HasOne
    {
        return $this->hasOne(ShipmentLocation::class);
    }
}
