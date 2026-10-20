<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->client);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect(['company_name', 'trade_name', 'federal_tax_id', 'national_id'])
            ->filter(fn (string $field) => is_string($this->input($field)))
            ->mapWithKeys(fn (string $field) => [$field => Str::upper(trim(preg_replace('/\s+/', ' ', $this->input($field))))])
            ->all());
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
