<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderExportStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->export);
    }

    public function rules(): array
    {
        return [
            'order_export_status_id' => 'required|integer|exists:order_export_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_export_status_id' => 'Estatus'
        ];
    }
}
