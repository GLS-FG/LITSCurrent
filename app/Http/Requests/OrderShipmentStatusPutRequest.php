<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderShipmentStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canAny(['update', 'restore'], $this->shipment);
    }

    public function rules(): array
    {
        return [
            'order_shipment_status_id' => 'required|integer|exists:order_shipment_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_shipment_status_id' => 'Estatus'
        ];
    }
}
