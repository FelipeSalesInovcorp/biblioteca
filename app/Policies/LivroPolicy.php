<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Livro;

class LivroPolicy
{
    /**
     * Permite ao admin tudo, antes de avaliar as outras regras.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true; // admin e cidadao podem listar
    }

    public function view(User $user, Livro $livro): bool
    {
        return true; // admin e cidadao podem ver detalhe
    }

    public function create(User $user): bool
    {
        return false; // cidadao NÃO
    }

    public function update(User $user, Livro $livro): bool
    {
        return false; // cidadao NÃO
    }

    public function delete(User $user, Livro $livro): bool
    {
        return false; // cidadao NÃO
    }
}

