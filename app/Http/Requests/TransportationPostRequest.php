<?php

namespace App\Http\Requests;

use App\Models\Transportation;
use Illuminate\Foundation\Http\FormRequest;

class TransportationPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Transportation::class);
    }

    public function rules(): array
    {
        return [
            'transportation_type' => 'required|max:255',
            'plates' => 'required|max:255',
            'driver' => 'nullable|max:255',
            'tracking_link' => 'nullable|max:500',
            'unit_eco_number' => 'nullable|max:255',
            'transportation_agency_id' => 'required|integer|exists:transportation_agencies,id',
            'vehicle_id' => 'nullable|integer|exists:vehicles,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'transportation_type' => 'Tipo de Transporte',
            'plates' => 'Placas',
            'driver' => 'Chofer',
            'tracking_link' => 'Enlace de rastreo',
            'unit_eco_number' => 'Unit Eco. Number',
            'transportation_agency_id' => 'Agencia',
            'vehicle_id' => 'Vehiculo',
        ];
    }
}
