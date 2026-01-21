<?php

namespace App\Http\Controllers\Chat;

use App\Actions\Chat\SendMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function store(Conversation $conversation, SendMessageRequest $request, SendMessage $action): RedirectResponse
    {
        $action->handle(auth()->user(), $conversation, $request->string('content'));

        return back();
    }
}
