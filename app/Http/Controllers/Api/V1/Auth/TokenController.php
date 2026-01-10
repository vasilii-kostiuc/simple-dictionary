<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{

    public function validateToken(Request $request): JsonResponse
    {
        if ($request->has('user_token')) {
            return $this->validateUserToken($request->input('user_token'));
        }

        $user = $request->user();

        return new ApiResponseResource([
            'data' => [
                'valid' => true,
                'user_id' => $user->id,
            ]
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    private function validateUserToken(string $token): JsonResponse
    {
        $token = str_replace('Bearer ', '', $token);

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return (new ApiResponseResource([
                'data' => [
                    'valid' => false,
                    'message' => 'Invalid token',
                ]
            ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return (new ApiResponseResource([
                'data' => [
                    'valid' => false,
                    'message' => 'Token expired',
                ]
            ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return (new ApiResponseResource([
                'data' => [
                    'valid' => false,
                    'message' => 'User not found',
                ]
            ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        return new ApiResponseResource([
            'data' => [
                'valid' => true,
                'user_id' => $user->id,
                'token_abilities' => $accessToken->abilities,
            ]
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

}
