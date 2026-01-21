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

        $conversation->load(['users', 'messages.user']);

        return view('chat.conversation', [
            'conversation' => $conversation,
        ]);
    }
}
