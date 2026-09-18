<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class CityPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', City::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required_without:new_state|integer|exists:states,id',
            'new_state' => 'sometimes|accepted',
            'new_state_name' => 'required_if_accepted:new_state|string|max:100',
            'new_state_short_name' => 'required_if_accepted:new_state|string|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'country_id' => 'País',
            'state_id' => 'Estado',
            'new_state' => 'Crear un nuevo estado',
            'new_state_name' => 'Nuevo Estado Nombre',
            'new_state_short_name' => 'Nuevo Estado Abreviado',
        ];
    }
}
