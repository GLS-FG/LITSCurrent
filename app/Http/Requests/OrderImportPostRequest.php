<?php

namespace App\Http\Requests;

use App\Models\OrderImport;
use Illuminate\Foundation\Http\FormRequest;

class OrderImportPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OrderImport::class);
    }

    public function rules(): array
    {
        return [
            'reference' => 'required|max:150',
            'comments' => 'nullable|max:500',
            'service_class_id' => 'required|exists:service_classes,id',
            'service_mode_id' => 'required|exists:service_modes,id',
            'class_type_id' => 'required|exists:class_types,id',
            'service_level_id' => 'required|exists:service_levels,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'reference' => 'Referencia',
            'petition' => 'Pedimento',
            'comments' => 'Comentarios',
            'total' => 'Valor comercial',
            'payment_date' => 'Fecha de pago',
            'petition_date' => 'Fecha de pedimento',
            'entry_date' => 'Fecha de entrada',
            'incoterm_id' => 'INCOTERM',
            'custom_id' => 'Aduana',
            'custom_agent_id' => 'Agente Aduanal',
            'service_class_id' => 'Service Class',
            'service_mode_id' => 'Service Mode',
            'class_type_id' => 'Class Type',
            'service_level_id' => 'Service Level',
            'incremental' => 'Incrementables',
            'customs_value' => 'Valor en aduana',
            'exchange_rate' => 'Tipo de cambio',
            'gross_weight' => 'Peso bruto',
            'packages' => 'Total de bultos',
            'fiscal_traffic_light' => 'Semáforo Fiscal',
        ];
    }
}
