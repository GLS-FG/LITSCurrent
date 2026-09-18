<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentDriverDocumentPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => 'required',
            'document_type_id.*' => 'integer|exists:document_types,id',
            'attachments' => 'required',
            'attachments.*' => 'file|max:26214400'
        ];
    }
}
