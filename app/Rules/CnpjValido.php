<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) !== 14) {
            $fail('O CNPJ deve conter 14 dígitos.');
            return;
        }

        if (preg_match('/^(\d)\1{13}$/', $digits)) {
            $fail('O CNPJ informado é inválido.');
            return;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights1[$i];
        }
        $r = $sum % 11;
        $d1 = $r < 2 ? 0 : 11 - $r;
        if ((int) $digits[12] !== $d1) {
            $fail('O CNPJ informado é inválido.');
            return;
        }

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $digits[$i] * $weights2[$i];
        }
        $r = $sum % 11;
        $d2 = $r < 2 ? 0 : 11 - $r;
        if ((int) $digits[13] !== $d2) {
            $fail('O CNPJ informado é inválido.');
        }
    }
}
