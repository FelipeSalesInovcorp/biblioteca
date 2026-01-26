<?php

namespace App\Http\Controllers\Chat;

use App\Actions\Chat\CreateRoom;
use App\Actions\Chat\InviteUsersToRoom;
use App\Actions\Chat\RemoveUserFromRoom;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\CreateRoomRequest;
use App\Http\Requests\Chat\InviteUsersRequest;
use App\Http\Requests\Chat\RemoveUserRequest;
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
        Gate::authorize('view', $room->conversation);

        $user = request()->user();

        // marca como lida ao abrir
        $room->conversation->users()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $room->load(['conversation.users', 'conversation.messages.user']);

        // usuários disponíveis para convidar
        $memberIds = $room->conversation->users->pluck('id');
        $availableUsers = \App\Models\User::query()
            ->whereNotIn('id', $memberIds)
            ->orderBy('name')
            ->get();

        $sidebar = app(\App\Services\ChatSidebarBuilder::class)->build($user);

        return view('chat.app', [
            'rooms' => $sidebar['rooms'],
            'directConversations' => $sidebar['directConversations'],
            'activeConversation' => $room->conversation,
            'activeRoom' => $room,
            'availableUsers' => $availableUsers,
        ]);
    }

    // Gerenciar membros da sala
    public function invite(Room $room, InviteUsersRequest $request, InviteUsersToRoom $action)
    {
        $action->handle($request->user(), $room, $request->input('user_ids'));

        return back();
    }

    public function remove(Room $room, RemoveUserRequest $request, RemoveUserFromRoom $action)
    {
        $action->handle($request->user(), $room, (int) $request->input('user_id'));

        return back();
    }

}

