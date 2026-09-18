<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderExportPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->export);
    }

    public function rules(): array
    {
        return [
            'reference' => 'required|max:150',
            'petition' => 'nullable|max:191',
            'comments' => 'nullable|max:500',
            'total' => 'nullable|numeric|gt:0',
            'payment_date' => 'nullable|date_format:d/m/Y',
            'petition_date' => 'nullable|date_format:d/m/Y',
            'currency_code' => 'required|string|max:3',
            'custom_id' => 'required|exists:customs,id',
            'custom_agent_id' => 'required|exists:custom_agents,id',
            'petition_code_id' => 'required|exists:petition_codes,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'reference' => 'Referencia',
            'petition' => 'Pedimento',
            'comments' => 'Comentarios',
            'total' => 'Costo de servicio',
            'payment_date' => 'Fecha de pago',
            'petition_date' => 'Fecha de pedimento',
        ];
    }
}
