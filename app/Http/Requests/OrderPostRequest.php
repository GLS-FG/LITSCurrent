<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Rules\SemicolonSeparatedEmails;
use Illuminate\Foundation\Http\FormRequest;

class OrderPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Order::class);
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'reference' => 'required|max:150',
            'contact_id' => 'required|exists:users,id',
            'carbon_copy' => ['nullable', new SemicolonSeparatedEmails()]

        ];
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'Cliente',
            'reference' => 'Referencia',
            'contact_id' => 'Usuario',
            'carbon_copy' => 'Receptores Adicionales'
        ];
    }
}
