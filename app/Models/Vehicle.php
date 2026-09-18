<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'eco_number', 'plates', 'caat_code', 'scac_code', 'vehicle_type', 'serial_number', 'origin',
        'transportation_agency_id', 'vehicle_brand', 'vehicle_model'
    ];



    public function transportationAgency(): BelongsTo
    {
        return $this->belongsTo(TransportationAgency::class);
    }
}
