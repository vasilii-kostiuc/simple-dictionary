<?php

namespace App\Http\Controllers\Api\V1\Language;

use App\Domain\Language\Models\Language;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Language\LanguageResource;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\SecurityScheme;

class LanguageController extends Controller
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
            )
        ]
    )]
    public function index()
    {
        return new ApiResponseResource(['data' => LanguageResource::collection(Language::all())]);
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
            )
        ]
    )]
    public function show(Language $language)
    {
        return new ApiResponseResource(['data' => new LanguageResource($language)]);
    }
}
