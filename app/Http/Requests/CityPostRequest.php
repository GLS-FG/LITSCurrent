<?php

namespace App\Http\Requests;

use App\Models\City;
use App\Models\State;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CityPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', City::class);
    }

    protected function prepareForValidation(): void
    {
        $upper = fn (string $field) => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input($field))));

        $this->merge(collect(['name', 'new_state_name', 'new_state_short_name'])
            ->filter(fn (string $field) => $this->has($field))
            ->mapWithKeys(fn (string $field) => [$field => $upper($field)])
            ->all());
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
            'new_state_name' => [
                'required_if_accepted:new_state', 'string', 'max:100',
                fn (string $attribute, mixed $value, Closure $fail) => $this->stateExists('name', $value)
                    && $fail('Ese estado ya existe en el país seleccionado, selecciónalo de la lista.'),
            ],
            'new_state_short_name' => [
                'required_if_accepted:new_state', 'string', 'max:50',
                fn (string $attribute, mixed $value, Closure $fail) => $this->stateExists('short_name', $value)
                    && $fail('Esa abreviación ya está en uso por un estado del país seleccionado.'),
            ],
        ];
    }

    // Compara sin importar mayúsculas, espacios sobrantes ni puntos ("SON." = "SON"), ignorando estados borrados.
    private function stateExists(string $column, mixed $value): bool
    {
        if (! $this->boolean('new_state') || ! is_string($value)) {
            return false;
        }
        $clean = fn (string $text) => str_replace('.', '', Str::upper(trim($text)));

        return State::where('country_id', $this->input('country_id'))
            ->whereRaw("REPLACE(UPPER(TRIM({$column})), '.', '') = ?", [$clean($value)])
            ->exists();
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
