<?php

namespace App\Http\Controllers\Chat;

use App\Actions\Chat\CreateDirectConversation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class DirectMessageController extends Controller
{
    public function store(User $user, CreateDirectConversation $action): RedirectResponse
    {
        $conversation = $action->handle(auth()->user(), $user);

        return redirect()->route('chat.conversations.show', $conversation);
    }
}
