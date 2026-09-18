<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLevel extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'code', 'name', 'class_type_id' ];
    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }
}
