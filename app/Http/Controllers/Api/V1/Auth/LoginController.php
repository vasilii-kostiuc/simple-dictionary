<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/v1/auth/login',
    operationId: 'login',
    description: 'Authenticate user and generate access token',
    summary: 'User login',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'remember', type: 'boolean', example: false),
            ]
        )
    ),
    tags: ['Authentication'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Successful login',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'access_token', type: 'string', example: '1|laravel_sanctum_token...'),
                    new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'The provided credentials are incorrect.'),
                    new OA\Property(property: 'errors', type: 'object'),
                ]
            )
        )
    ]
)]
class LoginController extends Controller
{
    public function __invoke(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $device = $request->userAgent() ?? '';
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));

        return response()->json([
            'access_token' => $user->createToken($device, expiresAt: $expiresAt)->plainTextToken,
            'user' => new UserResource($user),
        ], Response::HTTP_OK);
    }
}
