<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutorStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nome' => ['required','string','max:255'],
            'foto' => ['nullable','image','max:2048'],
        ];
    }
}
