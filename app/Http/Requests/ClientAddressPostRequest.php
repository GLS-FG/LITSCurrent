<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientAddressPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Client::class);
    }

    public function rules(): array
    {
        return [
            'contact_name' => 'required|string|min:3',
            'nickname' => 'nullable|max:256',
            'email' => 'required|email',
            'phone' => 'required|min:10|numeric',
            'address' => 'required|string',
            'neighborhood' => 'required|string|max:100',
            'postal_code' => 'required|min:5|numeric',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'contact_name' => 'Nombre de la persona de contacto',
            'nickname' => 'Apodo',
            'email' => 'Email',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'neighborhood' => 'Colonia',
            'postal_code' => 'Código postal',
            'city_id' => 'Ciudad',
            'state_id' => 'Estado',
            'country_id' => 'País',
        ];
    }
}
