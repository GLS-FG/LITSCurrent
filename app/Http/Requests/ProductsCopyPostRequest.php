<?php

namespace App\Http\Requests;

use App\Models\OrderProduct;
use Illuminate\Foundation\Http\FormRequest;

class ProductsCopyPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OrderProduct::class);
    }

    public function rules(): array
    {
        return [
            'copy_id' => 'required|numeric',
            'serviceable_id' => 'required|numeric',
            'copy_type' => 'required|string',
            'serviceable_type' => 'required|string'
        ];
    }

    public function attributes(): array
    {
        return [
            'copy_id' => 'Copiar a',
            'copy_type' => 'Copiar a tipo',
            'serviceable_id' => 'Servicio',
            'serviceable_type' => 'Servicio Tipo'
        ];
    }
}
