<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentDriverDocumentPutRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Order;
use App\Models\OrderShipment;
use App\View\Helpers\DocumentHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderShipmentDriverController extends Controller
{
    public function show(Order $order, OrderShipment $shipment)
    {
        return view('order-shipment.driver.show', [
            'order' => $order,
            'shipment' => $shipment,
            'documents' => DocumentType::where('for_drivers', 1)->get(),
        ]);
    }

    public function store(ShipmentDriverDocumentPutRequest $request, Order $order, OrderShipment $shipment)
    {
        $validated = $request->validated();
        $types = $request->input('document_type_id');
        $index = 0;
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments');
            $name = $file->getClientOriginalName();
            $size = Storage::size($path);
            $mime = Storage::mimeType($path);
            $document = new Document;
            $document->name = $path;
            $document->original_name = $name;
            $document->size_bytes = $size;
            $document->size_label = DocumentHelper::readableFileSize($size);
            $document->mime_type = $mime;
            $document->document_type_id = $types[$index];
            $shipment->documents()->save($document);
            $index += 1;
        }
        $shipment->driver_link_used = true;
        $shipment->save();
        return back()->with('success', 'Se han cargado los archivos con éxito.');
    }
}
