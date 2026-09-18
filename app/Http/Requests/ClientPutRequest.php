<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->client);
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string',
            'trade_name' => 'required|string',
            'federal_tax_id' => 'nullable|string',
            'national_id' => 'nullable|string',
            'phone1' => 'required|min:10|numeric',
            'image' => 'nullable|image|max:1000000',
            'email' => [
                'required',
                'email',
                Rule::unique('clients')->ignore($this->client->email, 'email')
            ]
        ];
    }
}
