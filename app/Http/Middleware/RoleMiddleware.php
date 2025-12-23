<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Normaliza para evitar problemas de maiúsculas/minúsculas
        $currentRole = strtolower((string) $user->role);
        $requiredRole = strtolower((string) $role);

        if ($currentRole !== $requiredRole) {
            abort(403);
        }

        return $next($request);
    }
}
