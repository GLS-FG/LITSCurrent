<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

class ServiceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ServiceType::class);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:10',
            'name' => 'required|string|max:256',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Código',
            'name' => 'Nombre'
        ];
    }
}
