<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Criar salas: só admin ativo.
     */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->isAdmin();
    }

    /**
     * Convidar/remover membros: só admin ativo (por agora).
     * (Depois, se quiseres, podemos permitir também "owner".)
     */
    public function invite(User $user, Room $room): bool
    {
        return $user->isActive() && $user->isAdmin();
    }

    public function remove(User $user, Room $room): bool
    {
        return $user->isActive() && $user->isAdmin();
    }
}
