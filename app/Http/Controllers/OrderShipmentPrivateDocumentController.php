<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateDocumentPostRequest;
use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\PrivateDocument;
use App\Models\PrivateDocumentType;
use App\View\Helpers\DocumentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class OrderShipmentPrivateDocumentController extends Controller
{
    public function index(Order $order, OrderShipment $shipment, Request $request)
    {
        if (!$request->has('privatefiles')) {
            abort(400, 'Debes seleccionar al menos dos archivos para descargar el ZIP del expediente.');
        }
        $selectedFiles = $request->input('privatefiles');
        if (count($selectedFiles) < 2) {
            abort(400, 'Debes seleccionar al menos dos archivos para descargar el ZIP del expediente.');
        }
        $zipFileName = 'ExpedienteInterno_' . $order->code .'_Embarque.zip';
        $zip = new ZipArchive();
        $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($shipment->privateDocuments as $document) {
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
        return view('order-shipment.private-document.create', [
            'order' => $order,
            'shipment' => $shipment,
            'documents' => PrivateDocumentType::all()
        ]);
    }

    public function store(PrivateDocumentPostRequest $request, Order $order, OrderShipment $shipment)
    {
        $validated = $request->validated();
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments');
            $name = $file->getClientOriginalName();
            $size = Storage::size($path);
            $mime = Storage::mimeType($path);
            $document = new PrivateDocument;
            $document->name = $path;
            $document->original_name = $name;
            $document->size_bytes = $size;
            $document->size_label = DocumentHelper::readableFileSize($size);
            $document->mime_type = $mime;
            $document->private_document_type_id = $validated['private_document_type_id'];
            $shipment->privateDocuments()->save($document);
        }
        return redirect()->route('orders.shipments.show', [ 'order' => $order, 'shipment' => $shipment ])->with('success', 'Archivo interno adjuntado correctamente.');
    }

    public function destroy(String $document)
    {
        $doc = PrivateDocument::findOrFail($document);
        $name = $doc->original_name;
        $doc->delete();
        return back()->with('success', 'El archivo ' . $name . ' ha sido eliminado de los adjuntos.');
    }
}
