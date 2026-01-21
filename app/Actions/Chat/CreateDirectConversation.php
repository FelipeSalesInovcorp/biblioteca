<?php

namespace App\Actions\Chat;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateDirectConversation
{
    public function handle(User $actor, User $otherUser): Conversation
    {
        if ($actor->id === $otherUser->id) {
            throw ValidationException::withMessages([
                'user' => 'Não podes iniciar conversa contigo próprio.',
            ]);
        }

        if (! $actor->isActive()) {
            throw ValidationException::withMessages([
                'user' => 'A tua conta não está ativa.',
            ]);
        }

        // chave determinística para garantir DM única
        $min = min($actor->id, $otherUser->id);
        $max = max($actor->id, $otherUser->id);
        $directKey = "{$min}-{$max}";

        return DB::transaction(function () use ($actor, $otherUser, $directKey) {
            $conversation = Conversation::query()
                ->where('type', 'direct')
                ->where('direct_key', $directKey)
                ->first();

            if ($conversation) {
                // garantir que ambos estão anexados (caso de dados inconsistentes)
                $conversation->users()->syncWithoutDetaching([
                    $actor->id => ['joined_at' => now()],
                    $otherUser->id => ['joined_at' => now()],
                ]);

                return $conversation;
            }

            $conversation = Conversation::create([
                'type' => 'direct',
                'direct_key' => $directKey,
                'created_by' => $actor->id,
            ]);

            $conversation->users()->attach([
                $actor->id => ['role' => null, 'joined_at' => now()],
                $otherUser->id => ['role' => null, 'joined_at' => now()],
            ]);

            return $conversation;
        });
    }
}
