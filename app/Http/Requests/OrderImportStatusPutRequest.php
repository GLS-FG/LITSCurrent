<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderImportStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['update', 'restore'], $this->import);
    }

    public function rules(): array
    {
        return [
            'order_import_status_id' => 'required|integer|exists:order_import_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_import_status_id' => 'Estatus'
        ];
    }
}
