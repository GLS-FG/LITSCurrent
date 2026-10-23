<?php

namespace App\Http\Requests;

use App\Models\PetitionCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PetitionCodePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PetitionCode::class);
    }

    protected function prepareForValidation(): void
    {
        foreach (['code', 'description', 'application_assumptions'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => Str::upper(trim((string) $this->input($field)))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:3', Rule::unique('petition_codes', 'code')->whereNull('deleted_at')],
            'description' => 'required|string|max:500',
            'application_assumptions' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Ya existe una clave de pedimento con ese código.',
        ];
    }
}
