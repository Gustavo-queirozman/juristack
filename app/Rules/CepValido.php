<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CepValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if ($digits === '') {
            return;
        }

        if (strlen($digits) !== 8) {
            $fail('O CEP deve conter 8 dígitos.');
        }
    }
}
