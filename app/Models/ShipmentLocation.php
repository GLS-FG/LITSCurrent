<?php

namespace App\Models;

use App\Enums\GeolocationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'geolocation_id', 'geolocation_status_id','order_shipment_id'
    ];

    public function geolocation(): BelongsTo
    {
        return $this->belongsTo(Geolocation::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(OrderShipment::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(GeolocationStatus::class, 'geolocation_status_id', 'id')->withTrashed();
    }
}
