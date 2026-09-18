<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->city);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'state_id' => 'required|integer|exists:states,id',
        ];
    }
}
