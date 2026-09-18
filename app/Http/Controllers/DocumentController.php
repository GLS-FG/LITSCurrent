<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(Document $document)
    {
        $headers = array(
            'Content-Type: '. $document->mime_type,
            'Content-Disposition' => 'inline; filename="'.$document->original_name.'"'
        );
        $filePath = storage_path('app/private/' . $document->name);
        return response()->file($filePath, $headers);
    }

    public function download(Document $document)
    {
        $headers = array(
            'Content-Type: '. $document->mime_type,
        );
        return Storage::download($document->name, $document->original_name, $headers);
    }
}
