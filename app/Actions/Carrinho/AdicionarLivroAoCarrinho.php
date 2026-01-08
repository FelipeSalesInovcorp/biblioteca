<?php

namespace App\Actions\Carrinho;

use App\Models\Carrinho;
use App\Models\CarrinhoItem;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdicionarLivroAoCarrinho
{
    /**
     * @throws ValidationException
     */
    public function execute(User $user, int $livroId): void
    {
        // Só cidadão
        if (($user->role ?? null) !== 'cidadao') {
            throw ValidationException::withMessages([
                'role' => 'Apenas cidadãos podem adicionar livros ao carrinho.',
            ]);
        }

        $livro = Livro::findOrFail($livroId);

        // Livro tem de ter preço
        if ($livro->preco === null) {
            throw ValidationException::withMessages([
                'preco' => 'Este livro não tem preço definido e não pode ser adicionado ao carrinho.',
            ]);
        }

        // Carrinho ativo 
        $carrinho = Carrinho::firstOrCreate(
            ['user_id' => $user->id, 'estado' => 'ativo'],
            ['user_id' => $user->id, 'estado' => 'ativo']
        );

        // Evitar duplicados no carrinho
        $jaExiste = CarrinhoItem::where('carrinho_id', $carrinho->id)
            ->where('livro_id', $livro->id)
            ->exists();

        if ($jaExiste) {
            throw ValidationException::withMessages([
                'livro' => 'Este livro já está no teu carrinho.',
            ]);
        }

        CarrinhoItem::create([
            'carrinho_id'    => $carrinho->id,
            'livro_id'       => $livro->id,
            'quantidade'     => 1,
            'preco_unitario' => $livro->preco,
        ]);
    }
}