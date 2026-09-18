<?php

namespace App\Http\Requests;

use App\Models\TransportationAgency;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;

class VehiclePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Vehicle::class);
    }

    public function rules(): array
    {
        return [
            'eco_number' => 'required|string|max:100',
            'plates' => 'nullable|string|max:100',
            'caat_code' => 'nullable|string|max:20',
            'scac_code' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'origin' => 'nullable|string|max:100',
            'vehicle_brand' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:10'
        ];
    }
}
