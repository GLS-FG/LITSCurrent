<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportationStatusPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->transportation);

    }

    public function rules(): array
    {
        return [
            'transportation_status_id' => 'required|integer|exists:transportation_statuses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'transportation_status_id' => 'Estatus'
        ];
    }
}
