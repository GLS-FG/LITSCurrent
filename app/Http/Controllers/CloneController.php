<?php

namespace App\Http\Controllers;

use App\Enums\OrderShipmentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Http\Requests\CloneShipmentPostRequest;
use App\Http\Requests\OrderPostRequest;
use App\Http\Requests\OrderShipmentPostRequest;
use App\Models\Client;
use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\Order;
use App\Models\OrderCopy;
use App\Models\OrderShipment;
use App\Models\OrderShipmentStatus;
use App\Models\OrderStatus;
use App\Models\PetitionCode;
use App\Models\ServiceClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CloneController extends Controller
{
    public function createOrder(OrderShipment $shipment)
    {
        return view('clone.create', [
            'statuses' => OrderStatus::all(),
            'from' => $shipment,
            'clients' => Client::select('id', 'name', 'last_name', 'company_name')->get()
        ]);
    }

    public function createShipment(OrderShipment $shipment, Order $order)
    {
        $company = $order->client->company_name;
        $instructionsOne = "*no chain allowed, use belt/strap to secure cargo ** please make loading appointment & confirm with supplier NON‐HAZ. Included";
        $instructionsTwo = '** Truck/Driver must meet security requirement for delivery to site*** need 24 hr alert before delivery ** can go straight to site through "Caseta de Vigilancia ** NO TAX APPLICABLE WHEN BILL TO GLS GROUP MEXICO';
        $serviceClasses = ServiceClass::where('service_type_id', 1)->get();
        $defaultServiceClass = ServiceClass::where('service_type_id', 1)->first();
        return view('clone.shipment.create', [
            'order' => $order,
            'from' => $shipment,
            'customs' => Custom::all(),
            'agents' => CustomAgent::all(),
            'codes' => PetitionCode::all(),
            'serviceClasses' => $serviceClasses,
            'defaultServiceClass' => $defaultServiceClass,
            'statuses' => OrderShipmentStatus::all(),
            'instructionsOne' => $instructionsOne,
            'instructionsTwo' => $instructionsTwo,
        ]);
    }

    public function storeOrder(OrderPostRequest $request, OrderShipment $shipment): RedirectResponse
    {
        $validated = $request->validated();
        $orderId = Order::whereYear('created_at', Carbon::now()->year)->count() + 1;
        $code = 'GLS-' . Carbon::now()->year . '-' . str_pad($validated['client_id'], 3, '0', STR_PAD_LEFT) . '-' . str_pad($orderId, 4, '0', STR_PAD_LEFT);
        $orderData = array_merge($validated, [ 'code' => $code, 'order_status_id' => OrderStatusEnum::ACTIVE, 'user_id' => Auth::id() ]);
        $newOrderId = DB::transaction(function () use ($orderData, $shipment) {
            $order = Order::create($orderData);
            OrderCopy::create(['order_shipment_id' => $shipment->id, 'order_id' => $order->id ]);
            return $order->id;
        }, 5);
        return redirect()->route('clone.order.shipments.create', [ 'shipment' => $shipment, 'order' => $newOrderId ]);
    }

    public function storeShipment(OrderShipment $shipment, Order $order, CloneShipmentPostRequest $request)
    {
        $validated = $request->validated();
        $validated = $request->safe()->except(['estimated_time_departure', 'estimated_time_arrival']);
        $orderId = OrderShipment::count() + 1;
        $trackingCode = 'GLS' . str_pad(Carbon::now()->month, 2, '0', STR_PAD_LEFT) . '02' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $trackingNumber = 'GLS' . Carbon::now()->timestamp;
        $data = array_merge($validated, [ 'order_id' => $order->id, 'tracking_code' => $trackingCode, 'order_shipment_status_id' => OrderShipmentStatusEnum::ACTIVE, 'tracking_number' => $trackingNumber ]);
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_departure');
        $data = $this->addDateToDataIfPresent($data, $request, 'estimated_time_arrival');
        $newShipment = OrderShipment::create($data);
        return redirect()->route('orders.shipments.show', [ 'order' => $order->id, 'shipment' => $newShipment->id ]);
    }

    private function addDateToDataIfPresent(array $data, FormRequest $request, String $dateField): array
    {
        $onlyDate = $request->safe()->only([$dateField]);
        if (!is_null($onlyDate[$dateField])) {
            $date = Carbon::createFromFormat('d/m/Y', $onlyDate[$dateField])->format('Y-m-d');
            return array_merge($data, [ $dateField => $date ]);
        }
        return $data;
    }
}
