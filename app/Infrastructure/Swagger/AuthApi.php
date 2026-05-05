<?php

namespace App\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

/**
 * Swagger annotations for Authentication & Profile endpoints.
 * Keeping annotations here keeps controllers clean.
 */
final class AuthApi
{
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'message', type: 'string', nullable: true, example: null),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'access_token', type: 'string', example: '1|laravel_sanctum_token...'),
                            new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
                        ]),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'The provided credentials are incorrect.'),
                        new OA\Property(property: 'errors', type: 'object'),
                        new OA\Property(property: 'data', nullable: true, example: null),
                    ]
                )
            ),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'User registration',
        description: 'Register new user and generate access token',
        operationId: 'register',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
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
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'message', type: 'string', nullable: true, example: null),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'access_token', type: 'string', example: '1|laravel_sanctum_token...'),
                            new OA\Property(property: 'user', type: 'object', ref: '#/components/schemas/Profile'),
                        ]),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                        new OA\Property(property: 'errors', type: 'object'),
                        new OA\Property(property: 'data', nullable: true, example: null),
                    ]
                )
            ),
        ]
    )]
    public function register(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'User logout',
        description: 'Revoke all user tokens',
        operationId: 'logout',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successfully logged out',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
                        new OA\Property(property: 'data', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/auth/token/validate',
        operationId: 'validateToken',
        summary: 'Validate access token',
        description: 'Validates the current Bearer token or a provided user_token. Returns user data if valid.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'user_token',
                        type: 'string',
                        nullable: true,
                        description: 'Optional token to validate. If omitted, validates the Bearer token from Authorization header.',
                        example: '1|laravel_sanctum_token...'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token is valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'valid', type: 'boolean', example: true),
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                            new OA\Property(property: 'avatar', type: 'string', nullable: true, example: null),
                        ]),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token is invalid or expired',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'valid', type: 'boolean', example: false),
                            new OA\Property(property: 'message', type: 'string', example: 'Token expired'),
                        ]),
                    ]
                )
            ),
        ]
    )]
    public function validateToken(): void
    {
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
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'message', type: 'string', nullable: true, example: null),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Profile', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function showProfile(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/profile',
        summary: 'Update user profile',
        description: 'Update authenticated user profile information',
        operationId: 'updateProfile',
        security: [['sanctum' => []]],
        tags: ['Profile'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'John Doe'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'john@example.com'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary', nullable: true),
                        new OA\Property(property: 'current_dictionary', type: 'integer', nullable: true, example: 1),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
                        new OA\Property(property: 'message', type: 'string', nullable: true, example: null),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Profile', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', type: 'object'),
                        new OA\Property(property: 'data', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function updateProfile(): void
    {
    }
}
