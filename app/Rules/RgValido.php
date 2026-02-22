<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RgValido implements ValidationRule
{
    /**
     * RG aceita formato variado por estado (números, ponto, hífen, X como dígito).
     * Validação mínima: apenas caracteres permitidos e tamanho razoável.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            return;
        }

        if (strlen($value) < 5 || strlen($value) > 20) {
            $fail('O RG deve ter entre 5 e 20 caracteres.');
            return;
        }

        if (! preg_match('/^[\d.\s\-Xx]+$/', $value)) {
            $fail('O RG contém caracteres inválidos. Use apenas números, pontos, hífens ou X.');
        }
    }
}
