<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportLocationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->import);
    }

    public function rules(): array
    {
        return [
            'comments' => 'nullable|max:191',
            'location_date' => 'required|date_format:Y-m-d\TH:i',
            'service_type_status_id' => 'required|integer|exists:service_type_statuses,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'comments' => 'Comentarios',
            'location_date' => 'Fecha',
            'service_type_status_id' => 'Estatus',
        ];
    }
}
