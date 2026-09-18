<?php

namespace App\Http\Requests;

use App\Models\TransportationAgency;
use Illuminate\Foundation\Http\FormRequest;

class TransportationAgencyPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', TransportationAgency::class);
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
            'company_name' => 'required|string|max:200|unique:transportation_agencies,company_name',
        ];
    }
}
