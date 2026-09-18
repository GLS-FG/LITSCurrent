<?php

namespace App\Models;

use App\Enums\TransportationStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transportation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transportation_type', 'plates','driver','transportation_status_id', 'transportation_agency_id',
        'order_shipment_id', 'ship_to', 'ship_from', 'tracking_link', 'unit_eco_number', 'vehicle_id'

    ];

    protected function casts(): array
    {
        return [
            'transportation_status_id' => TransportationStatusEnum::class,
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(OrderShipment::class);
    }

    public function geolocations(): HasMany
    {
        return $this->hasMany(Geolocation::class)->orderBy('location_date', 'desc');
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(Geolocation::class)->latestOfMany();
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(TransportationAgency::class, 'transportation_agency_id', 'id')->withTrashed();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->transportation_status_id != TransportationStatusEnum::CANCELED && $this->transportation_status_id != TransportationStatusEnum::CLOSED
        );
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNotIn('transportation_status_id', [TransportationStatusEnum::CANCELED, TransportationStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query): void
    {
        $query->where('transportation_status_id', TransportationStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query): void
    {
        $query->where('transportation_status_id', TransportationStatusEnum::CLOSED);
    }
}
