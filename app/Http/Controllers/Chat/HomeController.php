<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Rooms onde o user é membro (via conversation_user)
        $rooms = Room::query()
            ->whereHas('conversation.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with(['conversation'])
            ->orderBy('name')
            ->get();

        // DMs do user
        $directConversations = Conversation::query()
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
            'activeConversation' => null,
            'activeRoom' => null,
        ]);
    }
}
