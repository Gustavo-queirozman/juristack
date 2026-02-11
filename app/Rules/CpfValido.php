<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (strlen($digits) !== 11) {
            $fail('O CPF deve conter 11 dígitos.');
            return;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }
        $r = $sum % 11;
        $d1 = $r < 2 ? 0 : 11 - $r;
        if ((int) $digits[9] !== $d1) {
            $fail('O CPF informado é inválido.');
            return;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $digits[$i] * (11 - $i);
        }
        $r = $sum % 11;
        $d2 = $r < 2 ? 0 : 11 - $r;
        if ((int) $digits[10] !== $d2) {
            $fail('O CPF informado é inválido.');
        }
    }
}
