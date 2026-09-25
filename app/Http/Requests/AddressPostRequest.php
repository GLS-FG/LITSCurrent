<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;

class AddressPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Address::class);
    }

    public function rules(): array
    {
        return [
            'contact_name' => 'required|string|min:3',
            'name' => 'required|min:3|max:256',
            'trade_name' => 'required|min:3|max:256',
            'email' => 'nullable|email',
            'phone' => 'nullable|min:10|numeric',
            'address' => ['required', 'string', 'regex:/^[\p{L}\p{N}\s.,\/#-]+$/u'],
            'neighborhood' => 'nullable|string|min:3|max:100',
            'postal_code' => 'nullable|string|min:3|max:15',
            'city_id' => 'required|exists:cities,id',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',
            'link' => 'nullable|string',
            'location_name' => 'required|min:3|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'address.regex' => 'El campo :attribute solo permite letras, números, espacios y los caracteres . , - / #',
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
