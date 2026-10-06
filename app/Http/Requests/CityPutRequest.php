<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->city);
    }

    public function rules(): array
    {
        $nameRules = ['required', 'string', 'max:100'];
        if ($this->filled('state_id')) {
            $nameRules[] = Rule::unique('cities', 'name')
                ->where('state_id', $this->input('state_id'))
                ->whereNull('deleted_at')
                ->ignore($this->city->id);
        }

        return [
            'name' => $nameRules,
            'state_id' => 'required|integer|exists:states,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una ciudad con ese nombre en el estado seleccionado.',
        ];
    }
}
