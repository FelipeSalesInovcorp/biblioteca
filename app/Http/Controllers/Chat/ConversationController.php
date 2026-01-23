<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function show(Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        /*$conversation->load(['users', 'messages.user']);

        return view('chat.conversation', [
            'conversation' => $conversation,
        ]);*/

    $user = request()->user();

    // carregar dados da conversa ativa
    $conversation->load(['users', 'messages.user']);

    // Sidebar: Rooms onde o user é membro
    $rooms = \App\Models\Room::query()
        ->whereHas('conversation.users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
        ->with(['conversation'])
        ->orderBy('name')
        ->get();

    // Sidebar: DMs do user
    $directConversations = \App\Models\Conversation::query()
        ->where('type', 'direct')
        ->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
        ->with(['users'])
        ->orderByDesc('updated_at')
        ->get();

    return view('chat.app', [
        'rooms' => $rooms,
        'directConversations' => $directConversations,
        'activeConversation' => $conversation,
        'activeRoom' => null,
    ]);

    }
}
