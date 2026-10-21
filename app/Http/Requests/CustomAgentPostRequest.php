<?php

namespace App\Http\Requests;

use App\Models\CustomAgent;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CustomAgentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CustomAgent::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['company_name', 'name', 'last_name', 'patent', 'street_name', 'neighborhood'])
            ->filter(fn (string $field) => $this->has($field))
            ->mapWithKeys(fn (string $field) => [$field => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input($field))))])
            ->all());
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|min:3|max:200',
            'name' => 'required|string|min:3',
            'last_name' => 'required|string',
            'patent' => [
                'required', 'min:1', 'string',
                fn (string $attribute, mixed $value, Closure $fail) => $this->patentExists($value)
                    && $fail('Ya existe una agencia aduanal con esa patente.'),
            ],
            'phone1' => 'required|min:10|numeric',
            'street_name' => 'required|string',
            'street_no' => 'required|string|max:50',
            'neighborhood' => 'required|string|max:100',
            'postal_code' => 'required|min:5|numeric',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id'
        ];
    }

    // Misma patente sin importar mayúsculas ni espacios. Ignora las agencias borradas.
    // La razón social sí puede repetirse: una misma empresa tiene varios agentes con patentes distintas.
    private function patentExists(mixed $patent): bool
    {
        if (! is_string($patent)) {
            return false;
        }

        return CustomAgent::whereRaw('UPPER(TRIM(patent)) = ?', [Str::upper(trim($patent))])
            ->exists();
    }
}
