<?php

namespace App\Http\Requests;

use App\Models\CustomAgent;
use Illuminate\Foundation\Http\FormRequest;

class CustomAgentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CustomAgent::class);
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|min:3|max:200',
            'name' => 'required|string|min:3',
            'last_name' => 'required|string',
            'patent' => 'required|min:1|string',
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
