<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceClass extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'code', 'name', 'service_type_id' ];

    public function serviceModes(): HasMany
    {
        return $this->hasMany(ServiceMode::class);
    }
}
