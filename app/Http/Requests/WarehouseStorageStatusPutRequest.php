<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseStorageStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['update', 'restore'], $this->warehouse_storage);
    }

    public function rules(): array
    {
        return [
            'warehouse_storage_status_id' => 'required|integer|exists:warehouse_storage_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'warehouse_storage_status_id' => 'Estatus'
        ];
    }
}
