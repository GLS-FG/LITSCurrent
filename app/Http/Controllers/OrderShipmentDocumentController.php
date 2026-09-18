<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentPostRequest;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Order;
use App\Models\OrderShipment;
use App\View\Helpers\DocumentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OrderShipmentDocumentController extends Controller
{
    public function index(Order $order, OrderShipment $shipment, Request $request)
    {
        if (!$request->has('files')) {
            abort(400, 'Debes seleccionar al menos dos archivos para descargar el ZIP del expediente.');
        }
        $selectedFiles = $request->input('files');
        if (count($selectedFiles) < 2) {
            abort(400, 'Debes seleccionar al menos dos archivos para descargar el ZIP del expediente.');
        }
        $zipFileName = 'Expediente_' . $order->code .'_Embarque.zip';
        $zip = new ZipArchive();
        $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($shipment->documents as $document) {
            if(in_array($document->id, $selectedFiles)){
                $filePath = storage_path('app/private/' . $document->name);
                $zip->addFile($filePath, $document->original_name);
            }
        }
        $zip->close();
        return response()->download($zipFileName)->deleteFileAfterSend();
    }

    public function create(Order $order, OrderShipment $shipment)
    {
        return view('order-shipment.document.create', [
            'order' => $order,
            'shipment' => $shipment,
            'documents' => DocumentType::where('shipments', true)->orderBy('order_number')->get()
        ]);
    }

    public function store(DocumentPostRequest $request, Order $order, OrderShipment $shipment)
    {
        $validated = $request->validated();
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
            $document->document_type_id = $validated['document_type_id'];
            $shipment->documents()->save($document);
        }
        return redirect()->route('orders.shipments.show', [ 'order' => $order, 'shipment' => $shipment ])->with('success', 'Archivo adjuntado correctamente.');
    }

    public function destroy(Order $order, OrderShipment $shipment, Document $document)
    {
        $name = $document->original_name;
        $document->delete();
        return back()->with('success', 'El archivo ' . $name . ' ha sido eliminado de los adjuntos.');
    }
}
