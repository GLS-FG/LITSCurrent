<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WarehousePutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->warehouse);
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'contact_name'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input($field))))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('warehouses', 'name')->whereNull('deleted_at')
                    ->ignore($this->warehouse->id),
            ],
            'contact_name' => 'required|string|max:255',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe un almacén con ese nombre.',
        ];
    }
}
