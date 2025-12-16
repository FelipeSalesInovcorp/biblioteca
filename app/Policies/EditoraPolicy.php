<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Editora;

class EditoraPolicy
{
    /**
     * Admin pode tudo (executa antes de qualquer regra).
     */
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Listar editoras (admin e cidadão).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Ver detalhe da editora (admin e cidadão).
     */
    public function view(User $user, Editora $editora): bool
    {
        return true;
    }

    /**
     * Criar editora (só admin).
     */
    public function create(User $user): bool
    {
        return false; // cidadão NÃO
    }

    /**
     * Atualizar editora (só admin).
     */
    public function update(User $user, Editora $editora): bool
    {
        return false; // cidadão NÃO
    }

    /**
     * Apagar editora (só admin).
     */
    public function delete(User $user, Editora $editora): bool
    {
        return false; // cidadão NÃO
    }
}
