<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderProductPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('product'));
    }

    public function rules(): array
    {
        return [
            'product' => 'required|max:191',
            'weight' => 'nullable|max:191',
            'quantity' => 'nullable|numeric|gt:0',
            'container' => 'nullable|max:191',
            'value' => 'nullable|numeric',
            'reference' => 'required|max:191',
            'dimensions' => 'nullable|max:191',
            'height' => 'nullable|numeric|gt:0',
            'width' => 'nullable|numeric|gt:0',
            'length' => 'nullable|numeric|gt:0',
            'unit_measure' => 'nullable|max:20',
            'weight_measure' => 'required|max:20',
            'incoterm_id' => 'required|exists:incoterms,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'product' => 'Producto',
            'weight' => 'Peso',
            'quantity' => 'Cantidad',
            'container' => 'Contenedor',
            'value' => 'Valor',
            'reference' => 'Referencia',
            'dimensions' => 'Dimensiones',
            'height' => 'Alto',
            'width' => 'Ancho',
            'length' => 'Largo',
            'unit_measure' => 'Unidad de medida',
            'weight_measure' => 'Unidad de peso',
            'incoterm_id' => 'INCOTERM',
        ];
    }
}
