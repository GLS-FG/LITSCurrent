<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UserPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id'
        ];
    }
}
