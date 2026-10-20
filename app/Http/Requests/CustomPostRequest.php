<?php

namespace App\Http\Requests;

use App\Models\Custom;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CustomPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Custom::class);
    }

    protected function prepareForValidation(): void
    {
        $clean = fn (string $field) => trim(preg_replace('/\s+/', ' ', (string) $this->input($field)));

        $this->merge(collect(['code', 'denomination'])
            ->filter(fn (string $field) => $this->has($field))
            ->mapWithKeys(fn (string $field) => [$field => Str::upper($clean($field))])
            ->all());
    }

    public function rules(): array
    {
        return [
            'code' => 'required|max:4',
            'denomination' => [
                'required', 'string', 'max:150',
                fn (string $attribute, mixed $value, Closure $fail) => $this->customExists($value)
                    && $fail('Ya existe una aduana con esa denominación y esa sección.'),
            ],
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
        ];
    }

    // Mismo código y misma denominación, sin importar mayúsculas, espacios, puntos ni comas
    // ("NOGALES, SONORA." = "NOGALES SONORA"). Ignora las aduanas borradas.
    private function customExists(mixed $denomination): bool
    {
        if (! is_string($denomination)) {
            return false;
        }
        $clean = fn (string $text) => str_replace(['.', ','], '', Str::upper(trim(preg_replace('/\s+/', ' ', $text))));

        return Custom::where('code', $this->input('code'))
            ->whereRaw("REPLACE(REPLACE(UPPER(TRIM(denomination)), '.', ''), ',', '') = ?", [$clean($denomination)])
            ->exists();
    }
}
