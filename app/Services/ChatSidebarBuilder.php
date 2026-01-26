<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatSidebarBuilder
{
    /**
     * @return array{rooms: \Illuminate\Support\Collection<int, \App\Models\Room>, directConversations: \Illuminate\Support\Collection<int, \App\Models\Conversation>}
     */
    public function build(User $user): array
    {
        // Rooms do user + última mensagem + pivot do próprio user (para last_read_at)
        $rooms = Room::query()
            ->whereHas('conversation.users', fn ($q) => $q->where('users.id', $user->id))
            ->with([
                'conversation' => function ($q) {
                    // Para snippet/hora: 1 última mensagem
                    $q->with(['messages' => function ($mq) {
                        $mq->latest('created_at')->limit(1);
                    }]);
                },
                // Para unread: pivot do user autenticado
                'conversation.users' => fn ($q) => $q->where('users.id', $user->id),
            ])
            ->get()
            ->sortByDesc(fn ($room) => optional($room->conversation->messages->first())->created_at)
            ->values();

        // DMs do user + última mensagem + users (para label + pivot)
        $directConversations = Conversation::query()
            ->where('type', 'direct')
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->with([
                'users', // precisa para obter other user + pivot do auth user
                'messages' => fn ($mq) => $mq->latest('created_at')->limit(1),
            ])
            ->get()
            ->sortByDesc(fn ($c) => optional($c->messages->first())->created_at)
            ->values();

        return [
            'rooms' => $rooms,
            'directConversations' => $directConversations,
        ];
    }
}
