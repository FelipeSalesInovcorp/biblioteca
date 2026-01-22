<?php

namespace App\Http\Controllers\Chat;

use App\Actions\Chat\CreateRoom;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\CreateRoomRequest;
use App\Models\Room;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    public function create()
    {
        Gate::authorize('create', Room::class);

        return view('chat.rooms.create');
    }

    public function store(CreateRoomRequest $request, CreateRoom $action)
    {
        $user = $request->user();

        // upload do avatar pode entrar depois; por agora null
        $room = $action->handle(
            $user,
            $request->string('name'),
            $request->input('reference'),
            null
        );

        return redirect()->route('chat.rooms.show', $room);
    }

    public function show(Room $room)
    {
        // Quem pode ver? por agora: participantes (via ConversationPolicy view)
        Gate::authorize('view', $room->conversation);

        $room->load(['conversation.users', 'conversation.messages.user']);

        return view('chat.rooms.show', [
            'room' => $room,
            'conversation' => $room->conversation,
        ]);
    }
}

