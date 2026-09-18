<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehousePutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->warehouse);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
        ];
    }
}
