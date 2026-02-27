<?php

namespace App\Http\Middleware;

use App\Service\JwtTokenService;
use App\Http\Resources\ApiResponseResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class AuthenticateUserOrService
{
    private JwtTokenService $tokenService;

    public function __construct(JwtTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function handle(Request $request, Closure $next)
    {
        Log::info(__METHOD__ . ' called', ['path' => $request->path(), 'method' => $request->method()]);

        try {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                Auth::setUser($user);
                return $next($request);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

        $token = $request->bearerToken();
        if (!$token) {
            $header = $request->header('Authorization');
            if ($header && str_starts_with($header, 'Bearer ')) {
                $token = substr($header, 7);
            }
        }

        if ($token && $this->tokenService->isValidServiceToken($token)) {

            return $next($request);
        }

        Log::info('Unauthorized access attempt', ['path' => $request->path(), 'method' => $request->method()]);

        return (new ApiResponseResource([
            'message' => 'Unauthorized',
            'success' => false,
        ]))->response()->setStatusCode(401);
    }
}
