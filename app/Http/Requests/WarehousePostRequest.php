<?php

namespace App\Http\Requests;

use App\Models\Warehouse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WarehousePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Warehouse::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input('name'))))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('warehouses', 'name')->whereNull('deleted_at'),
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
