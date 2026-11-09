<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ScrapeGuardAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token') ?: ($request->bearerToken() ?? null);

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                auth()->setUser($accessToken->tokenable);
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}