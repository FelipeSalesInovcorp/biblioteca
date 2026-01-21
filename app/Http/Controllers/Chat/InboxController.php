<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;

class InboxController extends Controller
{
    public function index()
    {
        return view('chat.inbox', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }
}
