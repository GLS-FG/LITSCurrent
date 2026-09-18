<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class SemicolonSeparatedEmails implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $emails = explode(';', $value);
        foreach ($emails as $email) {
            $email = trim($email);
            if (empty($email)) {
                continue;
            }
            $validator = Validator::make(['email' => $email], ['email' => 'email']);
            if ($validator->fails()) {
                $fail("El campo Receptores Adicionales contiene una dirección de email inválida: {$email}.");
                return;
            }
        }
    }
}
