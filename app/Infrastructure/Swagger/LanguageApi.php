<?php

namespace App\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

/**
 * Swagger annotations for Language endpoints.
 */
final class LanguageApi
{
    #[OA\Get(
        path: '/api/v1/languages',
        operationId: 'listLanguages',
        description: 'Get list of available languages',
        summary: 'List all languages',
        security: [['sanctum' => []]],
        tags: ['Languages'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Language')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/languages/{language}',
        operationId: 'showLanguage',
        description: 'Get detailed information about specific language',
        summary: 'Show language details',
        security: [['sanctum' => []]],
        tags: ['Languages'],
        parameters: [
            new OA\Parameter(
                name: 'language',
                description: 'Language ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Language'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function show(): void
    {
    }
}
