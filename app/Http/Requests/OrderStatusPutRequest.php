<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['update', 'restore'], $this->order);
    }

    public function rules(): array
    {
        return [
            'order_status_id' => 'required|integer|exists:order_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_status_id' => 'Estatus'
        ];
    }
}
