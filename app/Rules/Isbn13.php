<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Isbn13 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isbn = preg_replace('/[^0-9]/', '', (string) $value);

        if (strlen($isbn) !== 13) {
            $fail('O :attribute deve ter exatamente 13 dígitos.');
            return;
        }

        // Recomendado: ISBN-13 costuma começar por 978 ou 979
        if (! str_starts_with($isbn, '978') && ! str_starts_with($isbn, '979')) {
            $fail('O :attribute deve começar por 978 ou 979.');
            return;
        }

        // Checksum ISBN-13 (EAN-13)
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $isbn[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        $check = (10 - ($sum % 10)) % 10;

        if ($check !== (int) $isbn[12]) {
            $fail('O :attribute é inválido (checksum incorreto).');
        }
    }
}
