<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderProductPostRequest;
use App\Http\Requests\OrderProductPutRequest;
use App\Models\Order;
use App\Models\OrderExport;
use App\Models\OrderProduct;

class OrderProductController extends Controller
{
    public function store(Order $order, OrderExport $export, OrderProductPostRequest $request)
    {
        $validated = $request->validated();
        $product = new OrderProduct();
        $product->order_id = $order->id;
        $product->product = $validated['product'];
        $product->weight = $validated['weight'];
        $product->container = $validated['container'];
        $product->quantity = $validated['quantity'];
        $product->value = $validated['value'];
        $product->insured = $validated['insured'];
        $product->reference = $validated['reference'];
        $product->dimensions = $validated['dimensions'];
        $product->height = $validated['height'];
        $product->width = $validated['width'];
        $product->length = $validated['length'];
        $product->unit_measure = $validated['unit_measure'];
        $product->haz_mat = $validated['haz_mat'];
        $product->incoterm = $validated['incoterm'];
        $export->products()->save($product);
        return back()->with('success', 'La mercancía ha sido creada.');
    }

    public function update(OrderProductPutRequest $request, Order $order, OrderProduct $product)
    {
        $product->update($request->validated());
        return back()->with('success', 'La mercancía ha sido actualizada.');
    }

    public function destroy(Order $order, OrderProduct $product)
    {
        $product->delete();
        return back()->with('success', 'La mercancía ha sido eliminada.');
    }
}
