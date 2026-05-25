<?php

namespace App\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

/**
 * Swagger annotations for Dictionary endpoints.
 */
final class DictionaryApi
{
    #[OA\Get(
        path: '/api/v1/dictionaries',
        operationId: 'listDictionaries',
        summary: 'List user dictionaries',
        description: 'Returns all dictionaries belonging to the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Dictionaries'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Dictionary')),
                    ]
                )
            ),
        ]
    )]
    public function index(): void {}

    #[OA\Get(
        path: '/api/v1/dictionaries/{dictionary}',
        operationId: 'showDictionary',
        summary: 'Show dictionary',
        description: 'Returns details of a specific dictionary',
        security: [['sanctum' => []]],
        tags: ['Dictionaries'],
        parameters: [
            new OA\Parameter(name: 'dictionary', in: 'path', required: true, description: 'Dictionary ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Dictionary', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function show(): void {}

    #[OA\Post(
        path: '/api/v1/dictionaries',
        operationId: 'storeDictionary',
        summary: 'Create dictionary',
        description: 'Creates a new dictionary for the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Dictionaries'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['language_from_id', 'language_to_id'],
                properties: [
                    new OA\Property(property: 'language_from_id', type: 'integer', description: 'Source language ID', example: 1),
                    new OA\Property(property: 'language_to_id', type: 'integer', description: 'Target language ID', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Dictionary created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Dictionary', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(): void {}

    #[OA\Delete(
        path: '/api/v1/dictionaries/{dictionary}',
        operationId: 'destroyDictionary',
        summary: 'Delete dictionary',
        description: 'Deletes a dictionary',
        security: [['sanctum' => []]],
        tags: ['Dictionaries'],
        parameters: [
            new OA\Parameter(name: 'dictionary', in: 'path', required: true, description: 'Dictionary ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Dictionary deleted successfully'),
        ]
    )]
    public function destroy(): void {}
}
