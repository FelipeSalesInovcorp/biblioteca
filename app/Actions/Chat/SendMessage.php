<?php

namespace App\Actions\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SendMessage
{
    public function handle(User $actor, Conversation $conversation, string $content): Message
    {
        Gate::authorize('sendMessage', $conversation);

        $content = trim($content);

        if ($content === '') {
            throw ValidationException::withMessages([
                'content' => 'A mensagem não pode estar vazia.',
            ]);
        }

        // limite simples para evitar abuso (podes ajustar)
        if (Str::length($content) > 5000) {
            throw ValidationException::withMessages([
                'content' => 'A mensagem é demasiado longa.',
            ]);
        }

        return DB::transaction(function () use ($actor, $conversation, $content) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $actor->id,
                'content' => $content,
            ]);

            // marca como "lido" para o autor
            $conversation->users()
                ->updateExistingPivot($actor->id, [
                    'last_read_at' => now(),
                ]);

            return $message;
        });
    }
}
