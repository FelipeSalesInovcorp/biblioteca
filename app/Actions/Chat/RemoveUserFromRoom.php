<?php

namespace App\Actions\Chat;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemoveUserFromRoom
{
    public function handle(User $actor, Room $room, int $userId): void
    {
        Gate::authorize('remove', $room);

        DB::transaction(function () use ($room, $userId) {

            // não remover o owner (simples e seguro)
            $pivot = $room->conversation->users()
                ->whereKey($userId)
                ->first()?->pivot;

            if (! $pivot) {
                // já não era membro, não faz nada
                return;
            }

            if (($pivot->role ?? null) === 'owner') {
                throw ValidationException::withMessages([
                    'user_id' => 'Não é possível remover o owner da sala.',
                ]);
            }

            $room->conversation->users()->detach($userId);
        });
    }
}
