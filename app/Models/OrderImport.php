<?php

namespace App\Models;

use App\Enums\OrderImportStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderImport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'reference', 'comments', 'petition', 'petition_date', 'custom_id', 'total',
        'payment_date', 'order_import_status_id', 'custom_agent_id', 'tracking_code',
        'entry_date', 'incremental', 'customs_value', 'exchange_rate', 'gross_weight', 'packages', 'incoterm_id',
        'fiscal_traffic_light', 'service_class_id', 'service_mode_id', 'class_type_id', 'service_level_id',
        'urgent', 'checklist_comments'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(CustomDeclaration::class);
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

    public function incoterm(): BelongsTo
    {
        return $this->belongsTo(Incoterm::class);
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

    public function status(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_import_status_id
        );
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_import_status_id != OrderImportStatusEnum::CANCELED && $this->order_import_status_id != OrderImportStatusEnum::CLOSED
        );
    }

    protected function casts(): array
    {
        return [
            'order_import_status_id' => OrderImportStatusEnum::class,
            'payment_date' => 'datetime:Y-m-d',
            'petition_date' => 'datetime:Y-m-d',
            'entry_date' => 'datetime:Y-m-d',
        ];
    }

    #[Scope]
    protected function active(Builder $query)
    {
        $query->whereNotIn('order_import_status_id', [OrderImportStatusEnum::CANCELED, OrderImportStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query)
    {
        $query->where('order_import_status_id', OrderImportStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query)
    {
        $query->where('order_import_status_id', OrderImportStatusEnum::CLOSED);
    }
}
