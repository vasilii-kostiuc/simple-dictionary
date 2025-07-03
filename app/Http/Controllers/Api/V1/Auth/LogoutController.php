<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/v1/auth/logout',
    summary: 'User logout',
    description: 'Revoke all user tokens',
    operationId: 'logout',
    tags: ['Authentication'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Successfully logged out',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
                ]
            )
        )
    ]
)]
class LogoutController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response(['message' => 'Logged out successfully']);
    }
}
