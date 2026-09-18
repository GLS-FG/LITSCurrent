<?php

namespace App\Models;

use App\Enums\OrderExportStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderExport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'reference', 'comments', 'petition', 'petition_code_id', 'petition_date', 'custom_id', 'total',
        'payment_date', 'order_export_status_id', 'currency_code', 'custom_agent_id'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function custom(): BelongsTo
    {
        return $this->belongsTo(Custom::class);
    }

    public function customAgent(): BelongsTo
    {
        return $this->belongsTo(CustomAgent::class);
    }

    public function petitionCode(): BelongsTo
    {
        return $this->belongsTo(PetitionCode::class);
    }

    public function products(): MorphMany
    {
        return $this->morphMany(OrderProduct::class, 'serviceable');
    }

    public function status(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_export_status_id
        );
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_export_status_id != OrderExportStatusEnum::CANCELED && $this->order_export_status_id != OrderExportStatusEnum::CLOSED
        );
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    protected function casts(): array
    {
        return [
            'order_export_status_id' => OrderExportStatusEnum::class,
            'payment_date' => 'datetime:Y-m-d',
            'petition_date' => 'datetime:Y-m-d',
        ];
    }

    #[Scope]
    protected function active(Builder $query)
    {
        $query->whereNotIn('order_export_status_id', [OrderExportStatusEnum::CANCELED, OrderExportStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query)
    {
        $query->where('order_export_status_id', OrderExportStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query)
    {
        $query->where('order_export_status_id', OrderExportStatusEnum::CLOSED);
    }
}
