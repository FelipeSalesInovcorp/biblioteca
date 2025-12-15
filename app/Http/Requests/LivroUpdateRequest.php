<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LivroUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $livro = $this->route('livro'); // vem do Route Model Binding

        return [
            'isbn'         => ['required', 'string', 'max:255', 'unique:livros,isbn,'.($livro?->id)],
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
