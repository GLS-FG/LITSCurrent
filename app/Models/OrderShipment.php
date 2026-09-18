<?php

namespace App\Models;

use App\Enums\OrderShipmentStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'comments','start_date','end_date','reference','instructions1','instructions2',
        'order_shipment_status_id','ship_to','ship_from','origin_city_id','origin_state_id','origin_country_id',
        'destination_city_id','destination_state_id','destination_country_id', 'tracking_code',
        'service_class_id', 'service_mode_id', 'class_type_id', 'service_level_id',
        'oversize', 'hazardous_material', 'refrigerated', 'insurance', 'tracking_number',
        'estimated_time_departure', 'estimated_time_arrival', 'urgent',
        'checklist_comments','tarps', 'ship_from_name', 'ship_to_name', 'ship_from_link', 'ship_to_link', 'show_map',
        'ship_from_id', 'ship_to_id'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shipFrom(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'ship_from_id');
    }

    public function shipTo(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'ship_to_id');
    }

    public function transportations(): HasMany
    {
        return $this->hasMany(Transportation::class);
    }

    public function locations(): MorphMany
    {
        return $this->morphMany(ServiceLocation::class, 'orderable');
    }

    public function latestLocation(): MorphOne
    {
        return $this->morphOne(ServiceLocation::class, 'orderable')->latestOfMany();
    }

    public function latestTransportations()
    {
        return $this->hasOne(Transportation::class)->latestOfMany();
    }

    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class);
    }

    public function customAgent(): BelongsTo
    {
        return $this->belongsTo(CustomAgent::class);
    }

    public function serviceClass(): BelongsTo
    {
        return $this->belongsTo(ServiceClass::class);
    }

    public function serviceMode(): BelongsTo
    {
        return $this->belongsTo(ServiceMode::class);
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    public function serviceLevel(): BelongsTo
    {
        return $this->belongsTo(ServiceLevel::class);
    }

    public function petitionCode(): BelongsTo
    {
        return $this->belongsTo(PetitionCode::class);
    }

    public function originCity(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function destinationCity(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function originState(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function destinationState(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function privateDocuments(): MorphMany
    {
        return $this->morphMany(PrivateDocument::class, 'documentable');
    }

    public function products(): MorphMany
    {
        return $this->morphMany(OrderProduct::class, 'serviceable');
    }

    public function status(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_shipment_status_id
        );
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_shipment_status_id != OrderShipmentStatusEnum::CANCELED && $this->order_shipment_status_id != OrderShipmentStatusEnum::CLOSED
        );
    }

    public function clonable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_shipment_status_id == OrderShipmentStatusEnum::CLOSED
        );
    }

    protected function casts(): array
    {
        return [
            'order_shipment_status_id' => OrderShipmentStatusEnum::class,
            'payment_date' => 'datetime:Y-m-d',
            'petition_date' => 'datetime:Y-m-d',
            'start_date' => 'datetime:Y-m-d',
            'end_date' => 'datetime:Y-m-d',
            'estimated_time_departure' => 'datetime:Y-m-d',
            'estimated_time_arrival' => 'datetime:Y-m-d',
            'show_map' => 'boolean'
        ];
    }

    #[Scope]
    protected function active(Builder $query)
    {
        $query->whereNotIn('order_shipment_status_id', [OrderShipmentStatusEnum::CANCELED, OrderShipmentStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query)
    {
        $query->where('order_shipment_status_id', OrderShipmentStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query)
    {
        $query->where('order_shipment_status_id', OrderShipmentStatusEnum::CLOSED);
    }
}
