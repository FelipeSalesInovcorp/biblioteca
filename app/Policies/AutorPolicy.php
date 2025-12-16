<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Autor;

class AutorPolicy
{
    /**
     * Admin pode tudo (executa antes de qualquer regra).
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Listar autores (admin e cidadão).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Ver detalhe do autor (admin e cidadão).
     */
    public function view(User $user, Autor $autor): bool
    {
        return true;
    }

    /**
     * Criar autor (só admin).
     */
    public function create(User $user): bool
    {
        return false; // cidadão NÃO
    }

    /**
     * Atualizar autor (só admin).
     */
    public function update(User $user, Autor $autor): bool
    {
        return false; // cidadão NÃO
    }

    /**
     * Apagar autor (só admin).
     */
    public function delete(User $user, Autor $autor): bool
    {
        return false; // cidadão NÃO
    }
}

