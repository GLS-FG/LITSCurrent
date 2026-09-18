<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomDeclarationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->import);
    }

    public function rules(): array
    {
        return [
            'custom_id' => 'required|exists:customs,id',
            'custom_agent_id' => 'required|exists:custom_agents,id',
            'petition' => 'required|max:191',
            'entry_date' => 'required|date_format:d/m/Y',
            'draft_date' => 'required|date_format:d/m/Y',
            'paid_date' => 'required|date_format:d/m/Y',
            'commercial_value' => 'required|numeric',
            'customs_value' => 'nullable|numeric',
            'customs_value_foreign' => 'required|numeric|gt:0',
            'exchange_rate' => 'nullable|numeric',
            'gross_weight' => 'required|numeric|gt:0',
            'packages' => 'required|numeric|gt:0',
            'incoterm_id' => 'required|exists:incoterms,id',
            'port_departure_id' => 'nullable|exists:customs,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'custom_id' => 'Aduana',
            'port_departure_id' => 'Puerto de salida',
            'custom_agent_id' => 'Agente Aduanal',
            'petition' => 'Pedimento',
            'entry_date' => 'Fecha de entrada',
            'draft_date' => 'Fecha de pedimento',
            'paid_date' => 'Fecha de pago',
            'commercial_value' => 'Valor comercial',
            'customs_value' => 'Valor en aduana(MXN)',
            'customs_value_foreign' => 'Valor en aduana(USD)',
            'exchange_rate' => 'Tipo de cambio',
            'gross_weight' => 'Peso bruto',
            'packages' => 'Total de bultos',
            'incoterm_id' => 'INCOTERM',
        ];
    }
}
