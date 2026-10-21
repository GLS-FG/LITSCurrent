<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CustomAgentAddressPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->custom_agent);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['street_name', 'neighborhood'])
            ->filter(fn (string $field) => is_string($this->input($field)))
            ->mapWithKeys(fn (string $field) => [$field => Str::upper(trim(preg_replace('/\s+/', ' ', $this->input($field))))])
            ->all());
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
