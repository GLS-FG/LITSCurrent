<?php

namespace App\Http\Controllers;

use App\Models\PrivateDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrivateDocumentController extends Controller
{
    public function show(PrivateDocument $private)
    {
        $headers = array(
            'Content-Type: '. $private->mime_type,
            'Content-Disposition' => 'inline; filename="'.$private->original_name.'"'
        );
        Log::debug($private);
        $filePath = storage_path('app/private/' . $private->name);
        return response()->file($filePath, $headers);
    }

    public function download(PrivateDocument $private)
    {
        $headers = array(
            'Content-Type: '. $private->mime_type,
        );
        return Storage::download($private->name, $private->original_name, $headers);
    }
}
