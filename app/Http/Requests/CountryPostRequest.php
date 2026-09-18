<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class CountryPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Country::class);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:5',
            'name' => 'required|string|max:100',
        ];
    }
}
