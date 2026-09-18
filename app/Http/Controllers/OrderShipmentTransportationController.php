<?php

namespace App\Http\Controllers;

use App\Enums\TransportationStatusEnum;
use App\Http\Requests\TransportationPostRequest;
use App\Http\Requests\TransportationPutRequest;
use App\Http\Requests\TransportationStatusPutRequest;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\Transportation;
use App\Models\TransportationAgency;
use App\Models\TransportationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class OrderShipmentTransportationController extends Controller
{
    public function create(Order $order, OrderShipment $shipment)
    {
        return view('order-shipment.transportation.create', [
            'order' => $order,
            'shipment' => $shipment,
            'agencies' => TransportationAgency::all(),
        ]);
    }

    public function store(Order $order, OrderShipment $shipment, TransportationPostRequest $request)
    {
        $validated = $request->validated();
        $data = array_merge($validated, [ 'transportation_status_id' => TransportationStatusEnum::ACTIVE, 'order_shipment_id' => $shipment->id ]);
        Transportation::create($data);
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ])
            ->with('success', 'Se creó correctamente el transporte.');
    }

    private function addDateToDataIfPresent(array $data, FormRequest $request, String $dateField): array
    {
        $onlyDate = $request->safe()->only([$dateField]);
        if (!is_null($onlyDate[$dateField])) {
            $date =  Carbon::createFromFormat('d/m/Y', $onlyDate[$dateField])->format('Y-m-d');
            return array_merge($data, [ $dateField => $date ]);
        }
        return $data;
    }

    public function edit(Order $order, OrderShipment $shipment, Transportation $transportation)
    {
        return view('order-shipment.transportation.edit', [
            'order' => $order,
            'shipment' => $shipment,
            'transportation' => $transportation,
            'agencies' => TransportationAgency::all(),
            'incoterms' => Incoterm::all(),
            'statuses' => TransportationStatus::all()
        ]);
    }

    public function update(Order $order, OrderShipment $shipment, TransportationPutRequest $request, Transportation $transportation)
    {
        $validated = $request->validated();
        $transportation->update($validated);
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ])
            ->with('success', 'Se creó correctamente el transporte.');
    }

    public function updateStatus(Order $order, OrderShipment $shipment, TransportationStatusPutRequest $request, Transportation $transportation)
    {
        $transportation->update($request->validated());
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $shipment->id ])
            ->with('success', 'Se actualizó correctamente el estatus del transporte.');
    }

    public function destroy(Order $order, OrderShipment $shipment, Transportation $transportation)
    {
        $transportation->delete();
        return back()->with('success', 'El transporte ha sido eliminado del embarque.');
    }
}
