<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomAgent extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'name', 'last_name', 'patent', 'phone1', 'company_name' ];

    public function customs(): BelongsToMany
    {
        return $this->belongsToMany(Custom::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomAgentAddress::class);
    }
}
