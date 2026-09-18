<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->custom);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|max:4',
            'denomination' => 'required|string|max:150',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
