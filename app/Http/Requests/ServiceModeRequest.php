<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

class ServiceModeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ServiceType::class);
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
