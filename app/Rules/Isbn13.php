<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Isbn13 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isbn = preg_replace('/\s+/', '', (string) $value);

        // Só dígitos
        if (!preg_match('/^\d+$/', $isbn)) {
            $fail('O :attribute deve conter apenas números.');
            return;
        }

        // Exatamente 13 dígitos
        if (strlen($isbn) !== 13) {
            $fail('O :attribute deve ter exatamente 13 dígitos.');
            return;
        }

        // Tem que começar por 978
        if (!str_starts_with($isbn, '978')) {
            $fail('O :attribute deve começar por 978.');
            return;
        }

        // Padrão exato: 978 + 10 dígitos (ou seja, 9780000000000)
        if (!preg_match('/^978\d{10}$/', $isbn)) {
            $fail('O :attribute deve seguir o padrão 9780000000000.');
            return;
        }
    }
}

