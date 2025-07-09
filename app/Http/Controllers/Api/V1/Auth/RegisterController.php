<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\UserResource;
use App\Service\UserService;
use OpenApi\Attributes as OA;



#[OA\Post(
    path: '/api/v1/auth/register',
    summary: 'User registration',
    description: 'Register new user and generate access token',
    operationId: 'register',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password', 'name'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Successful registration',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'access_token', type: 'string', example: '1|laravel_sanctum_token...'),
                    new OA\Property(property: 'user', type: 'object', ref: '#/components/schemas/User'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                    new OA\Property(property: 'errors', type: 'object'),
                ]
            )
        )
    ]
)]
class RegisterController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(RegisterUserRequest $request)
    {
        $user = $this->userService->register($request->get('email'), $request->get('password'), $request->get('name'));

        $device = $request->userAgent() ?? '';

        $accessToken = $user->createToken($device)->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'user' => new UserResource($user),
        ]);
    }
}
