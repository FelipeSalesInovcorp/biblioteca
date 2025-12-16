<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Isbn13;


class LivroStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('isbn')) {
            $this->merge([
                'isbn' => preg_replace('/[^0-9]/', '', (string) $this->input('isbn')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            /*'isbn'         => ['required', 'string', 'max:255', 'unique:livros,isbn'],*/
            'isbn' => ['required', new Isbn13, 'unique:livros,isbn'],
            'nome'         => ['required', 'string', 'max:255'],
            'editora_id'   => ['required', 'exists:editoras,id'],
            'bibliografia' => ['nullable', 'string'],
            'imagem_capa'  => ['nullable', 'image', 'max:2048'],
            'preco'        => ['required', 'numeric', 'min:0'],
            'autores'      => ['nullable', 'array'],
            'autores.*'    => ['integer', 'exists:autores,id'],
        ];
    }
}
