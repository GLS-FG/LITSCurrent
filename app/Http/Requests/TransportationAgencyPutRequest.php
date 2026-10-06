<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransportationAgencyPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->transportation_agency);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'contact_name' => 'required|string|max:100',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'rfc' => 'nullable|string|max:13',
            'caat_code' => 'nullable|string|max:4',
            'scac_code' => 'nullable|string|max:4',
            'company_name' => ['required', 'string', 'max:200', Rule::unique('transportation_agencies', 'company_name')->whereNull('deleted_at')->ignore($this->transportation_agency->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.unique' => 'Ya existe un transportista con esa razón social.',
        ];
    }
}
