<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->tokenCan('auth-token')) {
            $accessToken = Auth::user()->createToken('auth-token')->plainTextToken;

            // Attach the new access token to the response.
            return $next($request)->header('Authorization', 'Bearer ' . $accessToken);
        }

        return $next($request);
    }
}
