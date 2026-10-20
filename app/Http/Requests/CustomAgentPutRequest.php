<?php

namespace App\Http\Requests;

use App\Models\CustomAgent;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CustomAgentPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->custom_agent);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['company_name', 'name', 'last_name', 'patent'])
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
            'phone1' => 'required|min:10|numeric'
        ];
    }

    // Misma patente sin importar mayúsculas ni espacios. Ignora las agencias borradas y la propia al editar.
    // La razón social sí puede repetirse: una misma empresa tiene varios agentes con patentes distintas.
    private function patentExists(mixed $patent): bool
    {
        if (! is_string($patent)) {
            return false;
        }
        $ignoreId = $this->custom_agent->id;

        return CustomAgent::whereRaw('UPPER(TRIM(patent)) = ?', [Str::upper(trim($patent))])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
