<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticateOptional
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                Auth::setUser($user);
            }
        } catch (\Exception $e) {
            Log::debug('AuthenticateOptional: could not resolve user', ['error' => $e->getMessage()]);
        }

        return $next($request);
    }
}
