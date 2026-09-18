<?php

namespace App\Http\Requests;

use App\Models\PrivateDocument;
use Illuminate\Foundation\Http\FormRequest;

class PrivateDocumentPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PrivateDocument::class);
    }

    public function rules(): array
    {
        return [
            'private_document_type_id' => 'required|integer|exists:private_document_types,id',
            'attachments' => 'required',
            'attachments.*' => 'file|max:26214400'
        ];
    }
}
