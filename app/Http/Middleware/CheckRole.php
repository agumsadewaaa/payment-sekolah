<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:admin') or 'role:admin,teacher'
     */
    public function handle(Request $request, Closure $next, string $roles = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $allowed = array_map('trim', explode(',', $roles));

        $user = Auth::user();

        foreach ($allowed as $role) {
            if (method_exists($user, 'hasRole') ? $user->hasRole($role) : ($user->role === $role)) {
                return $next($request);
            }
        }

        // If we get here user is not in any allowed role
        abort(403, 'Forbidden — you do not have the required role.');
    }
}
