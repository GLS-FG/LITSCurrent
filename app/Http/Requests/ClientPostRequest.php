<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Client::class);
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string',
            'trade_name' => 'required|string',
            'federal_tax_id' => 'nullable|string',
            'national_id' => 'nullable|string',
            'email' => 'required|email|unique:clients,email',
            'phone1' => 'required|min:10|numeric',
            'street_name' => 'required|string',
            'street_no' => 'required|string|max:50',
            'neighborhood' => 'required|string|max:100',
            'postal_code' => 'required|min:5|numeric',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id'
        ];
    }
}
