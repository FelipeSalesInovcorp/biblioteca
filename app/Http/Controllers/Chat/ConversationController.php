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

        $user = request()->user();

        // marca como lida ao abrir (para desaparecer o ponto azul)
        $conversation->users()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $conversation->load(['users', 'messages.user']);

        $sidebar = app(\App\Services\ChatSidebarBuilder::class)->build($user);

        return view('chat.app', [
            'rooms' => $sidebar['rooms'],
            'directConversations' => $sidebar['directConversations'],
            'activeConversation' => $conversation,
            'activeRoom' => null,
        ]);
    }
}