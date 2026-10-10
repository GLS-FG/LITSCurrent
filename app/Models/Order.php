<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\View\Helpers\Service;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['client_id', 'reference', 'order_status_id', 'currency_code', 'code', 'user_id', 'carbon_copy', 'contact_id', 'urgent'];

    #[Scope]
    protected function active(Builder $query)
    {
        $query->whereNotIn('order_status_id', [OrderStatusEnum::CANCELED, OrderStatusEnum::CLOSED]);
    }

    #[Scope]
    protected function canceled(Builder $query)
    {
        $query->where('order_status_id', OrderStatusEnum::CANCELED);
    }

    #[Scope]
    protected function closed(Builder $query)
    {
        $query->where('order_status_id', OrderStatusEnum::CLOSED);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_id')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'order_status_id' => OrderStatusEnum::class,
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function storages(): HasMany
    {
        return $this->hasMany(WarehouseStorage::class);
    }

    public function imports(): HasMany
    {
        return $this->hasMany(OrderImport::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(OrderExport::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function status(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_status_id
        );
    }

    public function editable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_status_id != OrderStatusEnum::CANCELED && $this->order_status_id != OrderStatusEnum::CLOSED
        );
    }

    public function clonable(): Attribute
    {
        return new Attribute(
            get: fn () => $this->order_status_id == OrderStatusEnum::CLOSED
        );
    }

    public function allDocuments(){
        $documents = $this->documents;
        foreach($this->shipments as $shipment){
            $documents = $documents->merge($shipment->documents);
        }
        foreach($this->storages as $storage){
            $documents = $documents->merge($storage->documents);
        }
        foreach($this->imports as $import){
            $documents = $documents->merge($import->documents);
        }
        foreach($this->exports as $export){
            $documents = $documents->merge($export->documents);
        }
        return $documents;
    }
}
