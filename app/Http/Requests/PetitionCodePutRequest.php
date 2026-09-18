<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetitionCodePutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->petition_code);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:3',
            'description' => 'required|string|max:500',
            'application_assumptions' => 'required|string|max:500',
        ];
    }
}
