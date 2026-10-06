<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', City::class);
    }

    public function rules(): array
    {
        $nameRules = ['required', 'string', 'max:100'];
        if ($this->filled('state_id')) {
            $nameRules[] = Rule::unique('cities', 'name')
                ->where('state_id', $this->input('state_id'))
                ->whereNull('deleted_at');
        }

        return [
            'name' => $nameRules,
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required_without:new_state|integer|exists:states,id',
            'new_state' => 'sometimes|accepted',
            'new_state_name' => 'required_if_accepted:new_state|string|max:100',
            'new_state_short_name' => 'required_if_accepted:new_state|string|max:50',
        ];
    }

public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una ciudad con ese nombre en el estado seleccionado.',
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
