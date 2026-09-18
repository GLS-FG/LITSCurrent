<?php

namespace App\Http\Requests;

use App\Models\OrderShipment;
use Illuminate\Foundation\Http\FormRequest;

class CloneShipmentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', OrderShipment::class);
    }

    public function rules(): array
    {
        return [
            'comments' => 'nullable|max:1000',
            'reference' => 'required|max:150',
            'instructions1' => 'nullable|max:500',
            'instructions2' => 'nullable|max:500',
            'ship_to' => 'required|max:500',
            'ship_from' => 'required|max:500',
            'ship_from_name' => 'required|max:256',
            'ship_to_name' => 'required|max:256',
            'ship_from_link' => 'nullable|string',
            'ship_to_link' => 'nullable|string',
            'ship_from_id' => 'nullable|exists:addresses,id',
            'ship_to_id' => 'nullable|exists:addresses,id',
            'origin_city_id' => 'required|exists:cities,id',
            'origin_state_id' => 'required|exists:states,id',
            'origin_country_id' => 'required|exists:countries,id',
            'destination_city_id' => 'required|exists:cities,id',
            'destination_state_id' => 'required|exists:states,id',
            'destination_country_id' => 'required|exists:countries,id',
            'service_class_id' => 'required|exists:service_classes,id',
            'service_mode_id' => 'required|exists:service_modes,id',
            'class_type_id' => 'required|exists:class_types,id',
            'service_level_id' => 'required|exists:service_levels,id',
            'oversize' => 'required|max:2',
            'hazardous_material' => 'required|max:2',
            'tarps' => 'required|max:2',
            'refrigerated' => 'required|max:2',
            'insurance' => 'required|max:2',
            'estimated_time_departure' => 'required|date_format:d/m/Y',
            'estimated_time_arrival' => 'required|date_format:d/m/Y',
        ];
    }

    public function attributes(): array
    {
        return [
            'comments' => 'Comentarios',
            'reference' => 'Referencia',
            'instructions1' => 'Instrucciones de envio',
            'instructions2' => 'Instrucciones de consignia',
            'ship_to' => 'Dirección de entrega',
            'ship_from' => 'Dirección de recolección',
            'ship_from_link' => 'Ubicación maps de entrega',
            'ship_to_link' => 'Ubicación maps de recolección',
            'origin_city_id' => 'Ciudad origen',
            'origin_state_id' => 'Estado origen',
            'origin_country_id' => 'País origen',
            'destination_city_id' => 'Ciudad destino',
            'destination_state_id' => 'Estado destino',
            'destination_country_id' => 'País destino',
            'service_class_id' => 'Service Class',
            'service_mode_id' => 'Service Mode',
            'class_type_id' => 'Class Type',
            'service_level_id' => 'Service Level',
            'oversize' => 'Sobre dimensión',
            'hazardous_material' => 'Material peligroso',
            'tarps' => 'Tarps',
            'refrigerated' => 'Refrigerado',
            'insurance' => 'Seguro',
            'estimated_time_departure' => 'Estimated Time of Departure',
            'estimated_time_arrival' => 'Estimated Time of Arrival',
        ];
    }
}
