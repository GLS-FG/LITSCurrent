<?php

namespace App\Models;

use App\Enums\WarehouseStorageStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseStorage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'reference', 'comments', 'warehouse_id', 'warehouse_storage_status_id', 'tracking_code',
        'receipt', 'receipt_date', 'document', 'document_date', 'service_class_id', 'service_mode_id', 'class_type_id',
        'service_level_id', 'urgent', 'checklist_comments'
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
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

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function locations(): MorphMany
    {
        return $this->morphMany(ServiceLocation::class, 'orderable');
    }

    public function latestLocation(): MorphOne
    {
        return $this->morphOne(ServiceLocation::class, 'orderable')->latestOfMany();
    }

    public function privateDocuments(): MorphMany
    {
        return $this->morphMany(PrivateDocument::class, 'documentable');
    }

    public function products(): MorphMany
    {
        return $this->morphMany(OrderProduct::class, 'serviceable');
    }

    protected function casts(): array
    {
        return [
            'warehouse_storage_status_id' => WarehouseStorageStatusEnum::class,
            'receipt_date' => 'datetime:Y-m-d',
            'document_date' => 'datetime:Y-m-d',
        ];
    }

    public function status(): Attribute
    {
        return new Attribute(
            get: fn () => $this->warehouse_storage_status_id
        );
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->warehouse_storage_status_id != WarehouseStorageStatusEnum::CANCELED && $this->warehouse_storage_status_id != WarehouseStorageStatusEnum::CLOSED
        );
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNotIn('warehouse_storage_status_id', [WarehouseStorageStatusEnum::CANCELED, WarehouseStorageStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query): void
    {
        $query->where('warehouse_storage_status_id', WarehouseStorageStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query): void
    {
        $query->where('warehouse_storage_status_id', WarehouseStorageStatusEnum::CLOSED);
    }
}
