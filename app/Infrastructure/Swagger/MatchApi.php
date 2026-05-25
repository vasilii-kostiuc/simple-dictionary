<?php

namespace App\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

/**
 * Swagger annotations for Match, Match Step, and Match Step Attempt endpoints.
 */
final class MatchApi
{
    // ── Matches ───────────────────────────────────────────────────────────────

    #[OA\Post(
        path: '/api/v1/match-links',
        operationId: 'storeMatchLink',
        summary: 'Create a match link',
        description: 'Creates a public link for a future match and returns a QR SVG representation.',
        tags: ['Matches'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['match_type', 'match_type_params'],
                properties: [
                    new OA\Property(property: 'language_from_id', type: 'integer', nullable: true, example: 1, description: 'Optional. Defaults to English if omitted.'),
                    new OA\Property(property: 'language_to_id', type: 'integer', nullable: true, example: 2, description: 'Optional. Defaults to Russian if omitted.'),
                    new OA\Property(property: 'dictionary_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'match_type', type: 'string', enum: ['time', 'steps'], example: 'time'),
                    new OA\Property(property: 'match_type_params', type: 'object', example: '{"duration": 300}'),
                    new OA\Property(property: 'participants_limit', type: 'integer', nullable: true, example: 2),
                    new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Invite created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchLink', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Match link created successfully'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 429, description: 'Rate limit exceeded'),
        ]
    )]
    public function storeMatchLink(): void {}

    #[OA\Get(
        path: '/api/v1/match-links/{matchLink}',
        operationId: 'showMatchLink',
        summary: 'Show match link',
        description: 'Returns public static information for a previously created match link.',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'matchLink', in: 'path', required: true, description: 'Invite token', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Invite details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchLink', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Link not found'),
        ]
    )]
    public function showMatchLink(): void {}

    #[OA\Get(
        path: '/api/v1/matches',
        operationId: 'listMatches',
        summary: 'List matches',
        description: 'Returns all matches for the authenticated user or guest. Can be filtered by status.',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'filter[status]', in: 'query', required: false, description: 'Filter by status (new/in_progress/completed/cancelled)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'guest_id', in: 'query', required: false, description: 'Guest identifier (if not authenticated)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Match')),
                    ]
                )
            ),
        ]
    )]
    public function listMatches(): void {}

    #[OA\Post(
        path: '/api/v1/matches',
        operationId: 'storeMatch',
        summary: 'Create and start a match',
        description: 'Creates a new match with the given participants and immediately starts it',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['language_from_id', 'language_to_id', 'match_type', 'match_type_params', 'participants'],
                properties: [
                    new OA\Property(property: 'language_from_id', type: 'integer', description: 'Source language ID', example: 1),
                    new OA\Property(property: 'language_to_id', type: 'integer', description: 'Target language ID', example: 2),
                    new OA\Property(property: 'dictionary_id', type: 'integer', nullable: true, description: 'Optional dictionary ID', example: null),
                    new OA\Property(property: 'match_type', type: 'string', enum: ['time', 'steps'], description: 'Match completion type', example: 'time'),
                    new OA\Property(
                        property: 'match_type_params',
                        type: 'object',
                        description: 'Match type parameters. For time: {"duration": 120}. For steps: {"steps": 10}.',
                        example: '{"duration": 120}'
                    ),
                    new OA\Property(
                        property: 'participants',
                        type: 'array',
                        minItems: 2,
                        maxItems: 10,
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'type', type: 'string', enum: ['user', 'guest'], example: 'user'),
                                new OA\Property(property: 'id', type: 'string', description: 'User ID or guest ID', example: '42'),
                                new OA\Property(property: 'name', type: 'string', nullable: true, description: 'Display name. For guests: generated automatically if omitted.', example: 'Quick Fox 342'),
                                new OA\Property(property: 'avatar', type: 'string', nullable: true, description: 'Avatar URL. For guests: generated via DiceBear if omitted.', example: 'https://api.dicebear.com/7.x/avataaars/svg?seed=abc'),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Match created and started',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Match', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Match created successfully'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function storeMatch(): void {}

    #[OA\Get(
        path: '/api/v1/matches/{match}',
        operationId: 'showMatch',
        summary: 'Show match',
        description: 'Returns details of a specific match',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Match', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function showMatch(): void {}

    #[OA\Get(
        path: '/api/v1/matches/active',
        operationId: 'getActiveMatch',
        summary: 'Get active match',
        description: 'Returns the currently active (in_progress) match for the authenticated user or guest',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'guest_id', in: 'query', required: false, description: 'Guest identifier (if not authenticated)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active match or null',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Match', type: 'object', nullable: true),
                    ]
                )
            ),
        ]
    )]
    public function getActiveMatch(): void {}

    #[OA\Post(
        path: '/api/v1/matches/{match}/start',
        operationId: 'startMatch',
        summary: 'Start match',
        description: 'Starts a match that is in New status',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Match started',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Match started successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Match', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Match already started'),
        ]
    )]
    public function startMatch(): void {}

    #[OA\Post(
        path: '/api/v1/matches/{match}/complete',
        operationId: 'completeMatch',
        summary: 'Complete match',
        description: 'Manually completes an ongoing match',
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'reason', type: 'string', nullable: true, enum: ['time_expired', 'steps_completed', 'not_held', 'no_activity', 'all_players_left', 'forfeited', 'cancelled'], example: 'forfeited'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Match completed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Match completed successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Match', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Match already completed'),
        ]
    )]
    public function completeMatch(): void {}

    #[OA\Get(
        path: '/api/v1/matches/{match}/summary',
        operationId: 'matchSummary',
        summary: 'Get match summary',
        description: 'Returns a summary of the completed match including winner and scores',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchSummary', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function matchSummary(): void {}

    // ── Match Steps ───────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/matches/{match}/steps/{step}',
        operationId: 'showMatchStep',
        summary: 'Show match step',
        description: 'Returns details of a specific match step',
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchStep', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function showMatchStep(): void {}

    #[OA\Get(
        path: '/api/v1/matches/{match}/steps/next',
        operationId: 'nextMatchStep',
        summary: 'Get next match step',
        description: 'Generates and returns the next step for the participant in a match',
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'guest_id', in: 'query', required: false, description: 'Guest identifier (if not authenticated)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Next step generated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchStep', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Next step generated successfully'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Participant identification required'),
            new OA\Response(response: 409, description: 'Match finished or previous step not completed'),
        ]
    )]
    public function nextMatchStep(): void {}

    #[OA\Get(
        path: '/api/v1/matches/{match}/steps/current',
        operationId: 'currentMatchStep',
        summary: 'Get current match step',
        description: 'Returns the current unanswered step for the participant in a match',
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'guest_id', in: 'query', required: false, description: 'Guest identifier (if not authenticated)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current step returned',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchStep', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Participant identification required'),
            new OA\Response(response: 404, description: 'No current step found'),
            new OA\Response(response: 409, description: 'Match finished'),
        ]
    )]
    public function currentMatchStep(): void {}

    #[OA\Patch(
        path: '/api/v1/matches/{match}/steps/{step}/skip',
        operationId: 'skipMatchStep',
        summary: 'Skip match step',
        description: 'Marks a match step as skipped for the participant',
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'guest_id', in: 'query', required: false, description: 'Guest identifier (if not authenticated)', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Step skipped',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Step skipped successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/MatchStep', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Step does not belong to this participant'),
        ]
    )]
    public function skipMatchStep(): void {}

    // ── Match Step Attempts ───────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/matches/{match}/steps/{step}/attempts',
        operationId: 'listMatchStepAttempts',
        summary: 'List match step attempts',
        description: 'Returns all attempts for a specific match step. Can be filtered by correctness.',
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'is_correct', in: 'query', required: false, description: 'Filter by correctness (true/false)', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/MatchStepAttempt')),
                    ]
                )
            ),
        ]
    )]
    public function listMatchStepAttempts(): void {}

    #[OA\Post(
        path: '/api/v1/matches/{match}/steps/{step}/attempts',
        operationId: 'storeMatchStepAttempt',
        summary: 'Submit match step answer',
        description: 'Submit an answer attempt for a match step. The step must belong to the participant.',
        security: [['sanctum' => []]],
        tags: ['Match Steps'],
        parameters: [
            new OA\Parameter(name: 'match', in: 'path', required: true, description: 'Match ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['attempt_data', 'attempt_number', 'participant_type', 'participant_id'],
                properties: [
                    new OA\Property(property: 'attempt_data', type: 'object', description: 'Answer data (varies by step type)', example: '{"answer": "Привет"}'),
                    new OA\Property(property: 'attempt_number', type: 'integer', description: 'Attempt number (1-based)', example: 1),
                    new OA\Property(property: 'participant_type', type: 'string', enum: ['user', 'guest'], description: 'Type of participant', example: 'user'),
                    new OA\Property(property: 'participant_id', type: 'string', description: 'Authenticated user ID or guest ID, depending on participant_type', example: '42'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Attempt submitted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'attempt', ref: '#/components/schemas/MatchStepAttempt', type: 'object'),
                            new OA\Property(property: 'step', ref: '#/components/schemas/MatchStep', type: 'object'),
                        ]),
                        new OA\Property(property: 'message', type: 'string', example: 'Correct answer!'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Step does not belong to this participant'),
            new OA\Response(response: 409, description: 'Step already passed'),
        ]
    )]
    public function storeMatchStepAttempt(): void {}
}
