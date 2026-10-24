<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ServiceModeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ServiceType::class);
    }

    protected function prepareForValidation(): void
    {
        foreach (['code', 'name'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => Str::upper(trim(preg_replace('/\s+/', ' ', (string) $this->input($field))))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Código',
            'name' => 'Nombre',
            'icon' => 'Icono'
        ];
    }
}
