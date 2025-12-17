<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Requisicao;

class RequisicaoPolicy
{
    // Admin pode tudo; cidadão só as suas requisições
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null; // admin passa sempre
    }

    // Ambos podem ver menu (mas cidadão verá só as suas)
    public function viewAny(User $user): bool
    {
        return true; // ambos podem ver menu (mas cidadão verá só as suas)
    }

    // cidadão pode criar requisição
    public function create(User $user): bool
    {
        return $user->isCidadao(); // cidadão pode requisitar
    }
    
    // cidadão só vê as suas requisições
    public function view(User $user, Requisicao $requisicao): bool
    {
        return $requisicao->user_id === $user->id; // cidadão só vê as dele
    }
}
