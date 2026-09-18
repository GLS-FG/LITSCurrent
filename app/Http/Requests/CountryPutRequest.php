<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->country);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:5',
            'name' => 'required|string|max:100',
        ];
    }
}
