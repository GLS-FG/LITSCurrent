<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductPostRequest;
use App\Models\Incoterm;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\WarehouseStorage;

class OrderWarehouseStorageProductController extends Controller
{
    public function store(Order $order, WarehouseStorage $warehouseStorage, OrderProductPostRequest $request)
    {
        $validated = $request->validated();
        $items = $request->input('items', []);
        foreach ($items as $item) {
            $product = new OrderProduct();
            $product->order_id = $order->id;
            $product->product = $item['product'];
            $product->weight = $item['weight'];
            $product->container = $item['container'];
            $product->quantity = $item['quantity'];
            $product->value = $item['value'];
            $product->reference = $item['reference'];
            $product->dimensions = $item['dimensions'];
            $product->height = $item['height'];
            $product->width = $item['width'];
            $product->length = $item['length'];
            $product->unit_measure = $item['unit_measure'];
            $product->weight_measure = $item['weight_measure'];
            $product->incoterm_id = $item['incoterm_id'];
            $warehouseStorage->products()->save($product);
        }
        return redirect()->route('orders.warehouse-storages.show', [ 'order' => $order, 'warehouse_storage' => $warehouseStorage ])
            ->with('success', 'Se creó correctamente la mercancía.');
    }
}
