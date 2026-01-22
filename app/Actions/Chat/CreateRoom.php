<?php

namespace App\Actions\Chat;

use App\Models\Conversation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CreateRoom
{
    public function handle(User $actor, string $name, ?string $reference = null, ?string $avatarPath = null): Room
    {
        Gate::authorize('create', Room::class);

        $name = trim($name);
        $reference = $this->generateReference($name, $reference);

        return DB::transaction(function () use ($actor, $name, $reference, $avatarPath) {
            $conversation = Conversation::create([
                'type' => 'room',
                'created_by' => $actor->id,
            ]);

            $room = Room::create([
                'conversation_id' => $conversation->id,
                'name' => $name,
                'reference' => $reference,
                'avatar_path' => $avatarPath,
                'created_by' => $actor->id,
            ]);

            // Criador entra como owner
            $conversation->users()->attach([
                $actor->id => [
                    'role' => 'owner',
                    'joined_at' => now(),
                    'last_read_at' => now(),
                ],
            ]);

            return $room;
        });
    }

    private function generateReference(string $name, ?string $reference): string
    {
        $base = $reference ? trim($reference) : Str::slug($name);

        if ($base === '') {
            $base = 'sala';
        }

        // garante unicidade: base, base-2, base-3...
        $candidate = $base;
        $i = 2;

        while (Room::query()->where('reference', $candidate)->exists()) {
            $candidate = "{$base}-{$i}";
            $i++;
        }

        return $candidate;
    }
}
