<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UserPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user);
    }

    // También lo usan los contactos de un cliente (ClientUserController): su nombre se guarda igual en mayúsculas.
    protected function prepareForValidation(): void
    {
        if (! is_string($this->input('name'))) {
            return;
        }
        $this->merge(['name' => Str::upper(trim(preg_replace('/\s+/', ' ', $this->input('name'))))]);
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
