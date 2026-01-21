<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * O utilizador pode ver a conversa?
     * Regra: só participantes.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->users()
            ->whereKey($user->id)
            ->exists();
    }

    /**
     * O utilizador pode enviar mensagem?
     * Regra: participante + conta ativa.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        return $conversation->users()
            ->whereKey($user->id)
            ->exists();
    }
}
