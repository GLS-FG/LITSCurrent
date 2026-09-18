<?php

namespace App\Models;

use App\Enums\ClientAddressTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomAgentAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'custom_agent_id', 'address_type', 'street_name', 'street_no', 'neighborhood', 'postal_code',
        'city_id', 'state_id', 'country_id'
    ];

    public function customAgent(): BelongsTo
    {
        return $this->belongsTo(CustomAgent::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    protected function casts(): array
    {
        return [
            'address_type' => ClientAddressTypeEnum::class
        ];
    }
}
