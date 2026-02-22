<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfOuCnpjValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if ($digits === '') {
            return;
        }

        if (strlen($digits) === 11) {
            $rule = new CpfValido;
            $rule->validate($attribute, $value, $fail);
            return;
        }

        if (strlen($digits) === 14) {
            $rule = new CnpjValido;
            $rule->validate($attribute, $value, $fail);
            return;
        }

        $fail('O CPF deve ter 11 dígitos ou o CNPJ 14 dígitos.');
    }
}
