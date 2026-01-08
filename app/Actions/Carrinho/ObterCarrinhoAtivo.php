<?php

namespace App\Actions\Carrinho;

use App\Models\Carrinho;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ObterCarrinhoAtivo
{
    /**
     * Devolve o carrinho ativo do utilizador (com items e livros).
     * Não cria carrinho novo — apenas devolve o que existe.
     * @throws ValidationException
     */

    public function execute(User $user): ?Carrinho
    {
        if (($user->role ?? null) !== 'cidadao') {
            throw ValidationException::withMessages([
                'role' => 'Apenas cidadãos podem aceder ao carrinho.',
            ]);
        }

        return Carrinho::where('user_id', $user->id)
            ->where('estado', 'ativo')
            ->with(['items.livro'])
            ->first();
    }
}
