<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Domain\Training\Enums\TrainingStatus;
use App\Domain\Training\Models\Training;
use App\Domain\Training\Service\TrainingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingResource;
use Illuminate\Http\Response;

class TrainingController extends Controller
{
    private const TRAINING_CAN_BE_STARTED_ONLY_IN_NEW_STATE = 'training_can_be_started_only_in_new_state';

    private TrainingService $trainingService;


    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new ApiResponseResource(['data' => new TrainingResource($training)])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function start(Training $training)
    {
        if (TrainingStatus::from((int)$training->status) !== TrainingStatus::New) {
            return new ApiResponseResource(['message' => 'Training already started', 'errors' => [self::TRAINING_CAN_BE_STARTED_ONLY_IN_NEW_STATE => 'Training can be started only in new state']]);
        }

        $this->trainingService->start($training);
        return new ApiResponseResource(['message' => 'Training started successfully', 'data' => new TrainingResource($training)]);
    }

}
