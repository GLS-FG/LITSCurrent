<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomDeclarationInspectionPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->import);
    }

    public function rules(): array
    {
        return [
            'inspection' => 'required|boolean'
        ];
    }

    public function attributes(): array
    {
        return [
            'inspection' => 'Inspección'
        ];
    }
}
