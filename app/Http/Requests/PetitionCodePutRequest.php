<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PetitionCodePutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->petition_code);
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
            'code' => ['required', 'string', 'max:3', Rule::unique('petition_codes', 'code')->whereNull('deleted_at')->ignore($this->petition_code->id)],
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
