<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::query()->with('user')->latest();

        // filtros simples (opcional, mas útil)
        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        if ($request->filled('user')) {
            $user = $request->string('user');
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('name', 'like', "%{$user}%")
                  ->orWhere('email', 'like', "%{$user}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $modules = Log::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.logs.index', compact('logs', 'modules'));
    }
}
