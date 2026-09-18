<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomAgentPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->custom_agent);
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|min:3|max:200',
            'name' => 'required|string|min:3',
            'last_name' => 'required|string',
            'patent' => 'required|min:1|string',
            'phone1' => 'required|min:10|numeric'
        ];
    }
}
