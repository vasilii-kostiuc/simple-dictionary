<?php

namespace App\Infrastructure\Swagger;

use OpenApi\Attributes as OA;

/**
 * Swagger annotations for Training, Training Step, and Training Step Attempt endpoints.
 */
final class TrainingApi
{
    // ── Training ──────────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/trainings',
        operationId: 'listTrainings',
        summary: 'List trainings',
        description: 'Returns all trainings belonging to the authenticated user. Can be filtered by status.',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'filter[status]', in: 'query', required: false, description: 'Filter by status (1=New, 2=InProgress, 3=Completed)', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Training')),
                    ]
                )
            ),
        ]
    )]
    public function listTrainings(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/trainings',
        operationId: 'storeTraining',
        summary: 'Create training',
        description: 'Creates a new training session',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['training_type_id', 'dictionary_id', 'completion_type'],
                properties: [
                    new OA\Property(property: 'training_type_id', type: 'integer', enum: [1, 2, 3], description: '1=TopWords, 2=MyWords, 3=AllWords', example: 1),
                    new OA\Property(property: 'dictionary_id', type: 'integer', description: 'Dictionary ID to train on', example: 1),
                    new OA\Property(property: 'completion_type', type: 'string', enum: ['time', 'steps', 'unlimited'], description: 'How the training completes', example: 'steps'),
                    new OA\Property(property: 'completion_type_params', type: 'object', nullable: true, description: 'Params for completion type (e.g. {"steps": 10} or {"duration": 120})', example: '{"steps": 10}'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Training created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Training', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function storeTraining(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/trainings/{training}',
        operationId: 'showTraining',
        summary: 'Show training',
        description: 'Returns details of a specific training',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Training', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function showTraining(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/trainings/{training}/start',
        operationId: 'startTraining',
        summary: 'Start training',
        description: 'Starts a training that is in New status',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Training started or error message',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Training started successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Training', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function startTraining(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/trainings/{training}/expire',
        operationId: 'expireTraining',
        summary: 'Expire training',
        description: 'Completes a time-based training by expiration',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Training completed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Training completed successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Training', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Expiration not supported for this training type'),
        ]
    )]
    public function expireTraining(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/trainings/{training}/terminate',
        operationId: 'terminateTraining',
        summary: 'Terminate training',
        description: 'Forcefully terminates an ongoing training',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Training terminated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Training terminated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Training', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Training already completed'),
        ]
    )]
    public function terminateTraining(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/trainings/{training}/summary',
        operationId: 'trainingsSummary',
        summary: 'Get training summary',
        description: 'Returns a summary of the completed training session',
        security: [['sanctum' => []]],
        tags: ['Trainings'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingSummary', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function trainingSummary(): void
    {
    }

    // ── Training Steps ────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/trainings/{training}/steps/{step}',
        operationId: 'showTrainingStep',
        summary: 'Show training step',
        description: 'Returns details of a specific training step',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStep', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function showTrainingStep(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/trainings/{training}/steps/next',
        operationId: 'nextTrainingStep',
        summary: 'Get next training step',
        description: 'Generates and returns the next step for the training. The previous step must be completed or skipped.',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Next step generated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStep', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Next step generated successfully'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Training finished or previous step not completed'),
        ]
    )]
    public function nextTrainingStep(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/trainings/{training}/steps/current',
        operationId: 'currentTrainingStep',
        summary: 'Get current training step',
        description: 'Returns the current (most recent) step for the training',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current step returned',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStep', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Training finished or no current step found'),
        ]
    )]
    public function currentTrainingStep(): void
    {
    }

    #[OA\Patch(
        path: '/api/v1/trainings/{training}/steps/{step}/skip',
        operationId: 'skipTrainingStep',
        summary: 'Skip training step',
        description: 'Marks the current step as skipped',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Step skipped',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Step skipped successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStep', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function skipTrainingStep(): void
    {
    }

    #[OA\Get(
        path: '/api/v1/trainings/{training}/steps/{step}/progress',
        operationId: 'trainingStepProgress',
        summary: 'Get step progress',
        description: 'Returns the answering progress for a specific training step',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStepProgress', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function trainingStepProgress(): void
    {
    }

    // ── Training Step Attempts ─────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/trainings/{training}/steps/{step}/attempts',
        operationId: 'listTrainingStepAttempts',
        summary: 'List step attempts',
        description: 'Returns all attempts for a specific training step. Can be filtered by correctness.',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'is_correct', in: 'query', required: false, description: 'Filter by correctness (true/false)', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TrainingStepAttempt')),
                    ]
                )
            ),
        ]
    )]
    public function listTrainingStepAttempts(): void
    {
    }

    #[OA\Post(
        path: '/api/v1/trainings/{training}/steps/{step}/attempts',
        operationId: 'storeTrainingStepAttempt',
        summary: 'Submit step answer',
        description: 'Submit an answer attempt for a training step',
        security: [['sanctum' => []]],
        tags: ['Training Steps'],
        parameters: [
            new OA\Parameter(name: 'training', in: 'path', required: true, description: 'Training ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'step', in: 'path', required: true, description: 'Step ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'attempt_data', type: 'object', description: 'Answer data (varies by step type)', example: '{"answer": "Привет"}'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Attempt submitted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/TrainingStepAttempt', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 409, description: 'Step already passed'),
        ]
    )]
    public function storeTrainingStepAttempt(): void
    {
    }
}
