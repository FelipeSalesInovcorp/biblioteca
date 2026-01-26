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

        $sidebar = app(\App\Services\ChatSidebarBuilder::class)->build($user);

        return view('chat.app', [
            'rooms' => $sidebar['rooms'],
            'directConversations' => $sidebar['directConversations'],
            'activeConversation' => null,
            'activeRoom' => null,
        ]);
    }
}