<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportationGeolocationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->shipment);
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|max:191',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'comments' => 'nullable|max:191',
            'location_date' => 'required|date_format:Y-m-d\TH:i',
            'geolocation_status_id' => 'required|integer|exists:geolocation_statuses,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ubicación',
            'latitude' => 'Latitud',
            'longitude' => 'Longitud',
            'comments' => 'Comentarios',
            'location_date' => 'Fecha de geolocalización',
            'geolocation_status_id' => 'Estatus',
        ];
    }
}
