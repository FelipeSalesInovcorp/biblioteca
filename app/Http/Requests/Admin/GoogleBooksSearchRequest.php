<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GoogleBooksSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // a rota já está protegida por role:Admin
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function validatedQuery(): string
    {
        return trim((string) ($this->validated()['q'] ?? ''));
    }
}
