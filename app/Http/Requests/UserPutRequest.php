<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'client_id' => 'nullable|exists:clients,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'email' => 'Email',
            'password' => 'Contraseña',
            'role_id' => 'Permiso',
            'client_id' => 'Cliente'
        ];
    }
}
