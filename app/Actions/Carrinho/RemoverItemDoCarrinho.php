<?php

namespace App\Actions\Carrinho;

use App\Models\CarrinhoItem;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RemoverItemDoCarrinho
{
    /**
     * @throws ValidationException
     */
    public function execute(User $user, CarrinhoItem $item): void
    {
        if (($user->role ?? null) !== 'cidadao') {
            throw ValidationException::withMessages([
                'role' => 'Apenas cidadãos podem alterar o carrinho.',
            ]);
        }

        // Segurança: garantir que o item pertence ao carrinho do user
        if ($item->carrinho?->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'carrinho' => 'Não tens permissão para remover este item.',
            ]);
        }

        $item->delete();
    }
}
