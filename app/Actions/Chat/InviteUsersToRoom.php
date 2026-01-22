<?php

namespace App\Actions\Chat;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InviteUsersToRoom
{
    /**
     * @param array<int> $userIds
     */
    public function handle(User $actor, Room $room, array $userIds): void
    {
        Gate::authorize('invite', $room);

        DB::transaction(function () use ($room, $userIds) {
            $attachData = [];

            foreach ($userIds as $id) {
                $attachData[$id] = [
                    'role' => 'member',
                    'joined_at' => now(),
                    'last_read_at' => null,
                ];
            }

            $room->conversation->users()->syncWithoutDetaching($attachData);
        });
    }
}
