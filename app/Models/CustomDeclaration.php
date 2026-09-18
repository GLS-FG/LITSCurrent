<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomDeclaration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_import_id', 'custom_id', 'custom_agent_id', 'petition', 'entry_date', 'draft_date', 'paid_date',
        'commercial_value', 'customs_value', 'customs_value_foreign', 'exchange_rate', 'gross_weight', 'packages',
        'inspection', 'incoterm_id', 'port_departure_id'
    ];

    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class);
    }

    public function customAgent(): BelongsTo
    {
        return $this->belongsTo(CustomAgent::class);
    }

    public function incoterm(): BelongsTo
    {
        return $this->belongsTo(Incoterm::class);
    }

    protected function casts(): array
    {
        return [
            'entry_date' => 'datetime:Y-m-d',
            'draft_date' => 'datetime:Y-m-d',
            'paid_date' => 'datetime:Y-m-d',
        ];
    }
}
