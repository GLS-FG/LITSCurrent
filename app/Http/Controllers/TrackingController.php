<?php

namespace App\Http\Controllers;

use App\Models\OrderShipment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show(Request $request)
    {
        if ($request->has('tracking_code')) {
            $tracking_code = $request->get('tracking_code');
            try {
                $shipment = OrderShipment::where('tracking_code', $tracking_code)->firstOrFail();
                return view('tracking.show', [
                    'shipment' => $shipment,
                    'transportation' => $shipment->transportation,
                ]);
            } catch (ModelNotFoundException $e) {
                return view('tracking.show', [ 'notFound' => true ]);
            }
        } else {
            return view('tracking.show');
        }
    }
}
