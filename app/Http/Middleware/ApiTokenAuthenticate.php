<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Auth;

class ApiTokenAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Missing or invalid Authorization header. Must be a Bearer token.'
            ], 401);
        }

        $tokenStr = trim(substr($header, 7));
        $token = ApiToken::where('token', ApiToken::hashToken($tokenStr))->first();

        if (!$token) {
            $token = ApiToken::where('token', $tokenStr)->first();
            if ($token) {
                $token->update(['token' => ApiToken::hashToken($tokenStr)]);
            }
        }

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid API token.'
            ], 401);
        }

        // Update last used at timestamp
        $token->update(['last_used_at' => now()]);

        // Login the user dynamically in the request context
        Auth::login($token->user);

        return $next($request);
    }
}
