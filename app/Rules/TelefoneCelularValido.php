<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelefoneCelularValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if ($digits === '') {
            return;
        }

        $len = strlen($digits);
        if ($len === 10 || $len === 11) {
            if ($len === 11 && (int) $digits[2] !== 9) {
                $fail('Celular deve ter o nono dígito (9) na terceira posição.');
            }
            return;
        }

        $fail('O telefone/celular deve conter 10 ou 11 dígitos.');
    }
}
