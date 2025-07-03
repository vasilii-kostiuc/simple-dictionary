<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Resources\Auth\ProfileResource;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {

        $this->userService = $userService;
    }

    #[OA\Get(
        path: '/api/v1/profile',
        summary: 'Get user profile',
        description: 'Returns authenticated user profile information',
        operationId: 'showProfile',
        security: [['sanctum' => []]],
        tags: ['Profile'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(ref: '#/components/schemas/Profile')
            )
        ]
    )]
    public function show()
    {
        $user = Auth::user();

        return new ProfileResource($user);
    }

    #[OA\Patch(
        path: '/api/v1/profile',
        summary: 'Update user profile',
        description: 'Update authenticated user profile information',
        operationId: 'updateProfile',
        security: [['sanctum' => []]],
        tags: ['Profile'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'avatar', type: 'string', format: 'uri', example: 'https://example.com/avatar.jpg'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Profile')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function update(ProfileUpdateRequest $request)
    {
        $user = $this->userService->updateProfile(Auth::user(), $request->validated());

        return new ProfileResource($user);
    }
}
