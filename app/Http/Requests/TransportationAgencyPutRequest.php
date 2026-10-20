<?php

namespace App\Http\Requests;

use App\Models\TransportationAgency;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TransportationAgencyPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->transportation_agency);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['name', 'company_name', 'contact_name'])
            ->filter(fn (string $field) => $this->has($field))
            ->mapWithKeys(fn (string $field) => [$field => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input($field))))])
            ->all());
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'contact_name' => 'required|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'rfc' => 'nullable|string|max:13',
            'caat_code' => 'nullable|string|max:4',
            'scac_code' => 'nullable|string|max:4',
            'company_name' => [
                'required', 'string', 'max:200',
                fn (string $attribute, mixed $value, Closure $fail) => $this->companyExists($value)
                    && $fail('Ya existe un transportista con ese nombre comercial.'),
            ],
        ];
    }

    // Mismo nombre comercial (company_name) que otro transportista, sin importar mayúsculas, espacios, puntos ni
    // comas ("S.A. DE C.V." = "SA DE CV"). La razón social (name) puede repetirse a propósito.
    // Ignora los transportistas borrados y el propio al editar.
    private function companyExists(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $clean = fn (string $text) => str_replace(['.', ','], '', Str::upper(trim(preg_replace('/\s+/', ' ', $text))));
        $ignoreId = $this->transportation_agency->id;

        return TransportationAgency::whereRaw("REPLACE(REPLACE(UPPER(TRIM(company_name)), '.', ''), ',', '') = ?", [$clean($value)])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
