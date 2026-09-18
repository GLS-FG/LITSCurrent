<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('address'));
    }

    public function rules(): array
    {
        return [
            'contact_name' => 'required|string|min:3',
            'name' => 'required|max:256',
            'trade_name' => 'required|max:256',
            'email' => 'nullable|email',
            'phone' => 'nullable|min:10|numeric',
            'address' => 'required|string',
            'neighborhood' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:15',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'link' => 'nullable|string',
            'location_name' => 'required|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre de la dirección',
            'trade_name' => 'Nombre comercial',
            'contact_name' => 'Nombre de la persona de contacto',
            'email' => 'Email',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'neighborhood' => 'Colonia',
            'postal_code' => 'Código postal',
            'city_id' => 'Ciudad',
            'state_id' => 'Estado',
            'country_id' => 'País',
            'link' => 'Ubicación en maps',
            'location_name' => 'Nombre de la dirección',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
        ];
    }
}
