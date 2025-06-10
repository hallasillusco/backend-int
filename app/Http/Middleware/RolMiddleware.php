<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->rol->nombre, $roles)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}
