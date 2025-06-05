<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingResource;
use App\Http\Resources\Training\TrainingStepAttemptResource;
use App\Http\Resources\Training\TrainingStepResource;
use App\Training\Enums\TrainingStatus;
use App\Training\Factories\CompletionConditionFactory;
use App\Training\Factories\TrainingStrategyFactory;
use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Service\StepCheckService;
use App\Training\Service\TrainingService;
use App\Training\Service\TrainingStepAttemptService;
use App\Training\Service\TrainingStepService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrainingController extends Controller
{
    private const ERROR_TRAINING_FINISHED = 'training_finished';
    private const ERROR_STEP_NOT_COMPLETED = 'previous_step_not_completed';

    private TrainingService $trainingService;
    private StepCheckService $stepCheckService;
    private TrainingStepAttemptService $trainingStepAttemptService;
    private TrainingStrategyFactory $trainingStrategyFactory;
    private CompletionConditionFactory $completionConditionFactory;

    public function __construct(
        TrainingStrategyFactory $trainingStrategyFactory,
        TrainingService $trainingService,
        TrainingStepAttemptService $trainingStepAttemptService,
        StepCheckService $stepCheckService,
        CompletionConditionFactory $completionConditionFactory
    ) {
        $this->trainingStrategyFactory = $trainingStrategyFactory;
        $this->trainingService = $trainingService;
        $this->trainingStepAttemptService = $trainingStepAttemptService;
        $this->stepCheckService = $stepCheckService;
        $this->completionConditionFactory = $completionConditionFactory;
    }


    public function store(StoreTrainingRequest $request)
    {
        $training = $this->trainingService->create($request->validated());

        return new ApiResponseResource(['data' => new TrainingResource($training)])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function start(Training $training)
    {
        if (TrainingStatus::from($training->status) !== TrainingStatus::New) {
            return new ApiResponseResource(['message' => 'Training already started', 'errors' => ['training_can_be_started_only_in_new_state' => 'Training can be started only in new state']]);
        }

        $this->trainingService->start($training);
        return new ApiResponseResource(['message' => 'Training started successfully', 'data' => new TrainingResource($training)]);
    }


    public function nextStep(Training $training)
    {
        if ($training->status == TrainingStatus::Finished) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'errors' => [self::ERROR_TRAINING_FINISHED => 'Training is finished'],
                    'message' => 'Training is finished',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        if (!$this->trainingService->isLastStepCompletedOrSkipped($training)) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'errors' => [self::ERROR_STEP_NOT_COMPLETED => 'Previous step is not completed'],
                    'message' => 'New step can be created only after compliting prev step',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);;
        }

        $generatedStep = $this->trainingStrategyFactory->create($training)->generateNextStep();

        $nextStep = new TrainingStepService()->create($generatedStep, $training);
        return ApiResponseResource::make(['data' => new TrainingStepResource($nextStep), 'message' => 'Next step generated successfully']);
    }

    public function completeStep(Training $training, TrainingStep $step, Request $request)
    {
        $attemptData = $request->all('attempt_data');

        $isPassed = $this->stepCheckService->check($step, $attemptData);

        $attempt = $this->trainingStepAttemptService->create($step->id, $attemptData, $isPassed);

        $completionCondition = $this->completionConditionFactory->create($training);

        if ($completionCondition->isCompleted()) {
            $this->trainingService->setCompleted($training);
        }

        return new TrainingStepAttemptResource($attempt);
    }
}
