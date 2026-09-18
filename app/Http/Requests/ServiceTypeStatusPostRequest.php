<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

class ServiceTypeStatusPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ServiceType::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'color' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'color' => 'Color',
            'permissions' => 'Service Modes'
        ];
    }
}
