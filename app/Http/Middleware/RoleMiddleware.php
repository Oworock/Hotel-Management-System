<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is deactivated.']);
        }

        // Check roles and functions
        foreach ($roles as $roleOrFunc) {
            if ($user->role === $roleOrFunc) {
                return $next($request);
            }
            if ($user->hasFunction($roleOrFunc)) {
                return $next($request);
            }
        }

        // Abort or redirect
        abort(403, 'Unauthorized action. You do not have the required role or permission.');
    }
}
