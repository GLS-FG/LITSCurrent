<?php

namespace App\Http\Requests;

use App\Models\WarehouseStorage;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoragePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', WarehouseStorage::class);
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'reference' => 'required|max:150',
            'comments' => 'nullable|max:500',
            'receipt' => 'required|string|max:191',
            'receipt_date' => 'required|date_format:d/m/Y',
            'document' => 'required|string|max:191',
            'document_date' => 'required|date_format:d/m/Y',
            'service_class_id' => 'required|exists:service_classes,id',
            'service_mode_id' => 'required|exists:service_modes,id',
            'class_type_id' => 'required|exists:class_types,id',
            'service_level_id' => 'nullable|exists:service_levels,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'reference' => 'Referencia',
            'warehouse_id' => 'Almacén',
            'comments' => 'Comentarios',
            'receipt' => 'Recibido de',
            'receipt_date' => 'Fecha recibo',
            'document' => 'Documento',
            'document_date' => 'Fecha documento',
            'service_class_id' => 'Service Class',
            'service_mode_id' => 'Service Mode',
            'class_type_id' => 'Class Type',
            'service_level_id' => 'Service Level',
        ];
    }
}
