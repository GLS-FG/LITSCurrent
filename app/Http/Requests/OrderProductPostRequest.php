<?php

namespace App\Http\Requests;

use App\Models\OrderProduct;
use Illuminate\Foundation\Http\FormRequest;

class OrderProductPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OrderProduct::class);
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product' => 'required|max:191',
            'items.*.reference' => 'required|max:191',
            'items.*.length' => 'nullable|numeric|gt:0|required_without:items.*.dimensions',
            'items.*.width' => 'nullable|numeric|gt:0|required_without:items.*.dimensions',
            'items.*.height' => 'nullable|numeric|gt:0|required_without:items.*.dimensions',
            'items.*.unit_measure' => 'nullable|max:20',
            'items.*.dimensions' => 'nullable|max:191|required_without:items.*.height,items.*.width,items.*.length',
            'items.*.weight' => 'nullable|max:191',
            'items.*.weight_measure' => 'required|max:20',
            'items.*.container' => 'nullable|max:191',
            'items.*.quantity' => 'nullable|numeric|gt:0',
            'items.*.value' => 'nullable|numeric',
            'items.*.incoterm_id' => 'required|exists:incoterms,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'items.*.product' => 'Producto',
            'items.*.weight' => 'Peso',
            'items.*.quantity' => 'Cantidad',
            'items.*.container' => 'Contenedor',
            'items.*.value' => 'Valor',
            'items.*.reference' => 'Referencia',
            'items.*.dimensions' => 'Dimensiones',
            'items.*.height' => 'Alto',
            'items.*.width' => 'Ancho',
            'items.*.length' => 'Largo',
            'items.*.unit_measure' => 'Unidad de medida',
            'items.*.weight_measure' => 'Unidad de peso',
            'items.*.incoterm_id' => 'INCOTERM',
        ];
    }
}
