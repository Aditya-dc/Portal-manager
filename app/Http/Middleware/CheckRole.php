<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($role === 'superadmin' && !$user->isSuperAdmin()) {
            abort(403);
        }

        if ($role === 'admin' && !($user->isSuperAdmin() || $user->isAdmin())) {
            abort(403);
        }

        return $next($request);
    }
}
