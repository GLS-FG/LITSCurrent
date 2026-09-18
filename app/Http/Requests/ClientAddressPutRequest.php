<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientAddressPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->client);
    }

    public function rules(): array
    {
        return [
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
