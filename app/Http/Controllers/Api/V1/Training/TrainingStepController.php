<?php

namespace App\Http\Controllers\Api\V1\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Training\StoreTrainingRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Training\TrainingResource;
use App\Http\Resources\Training\TrainingStepAttemptResource;
use App\Http\Resources\Training\TrainingStepProgressResource;
use App\Http\Resources\Training\TrainingStepResource;
use App\Training\Enums\TrainingStatus;
use App\Training\Factories\CompletionConditionFactory;
use App\Training\Factories\TrainingStrategyFactory;
use App\Training\Models\Training;
use App\Training\Models\TrainingStep;
use App\Training\Service\StepCheckService;
use App\Training\Service\TrainingService;
use App\Training\Service\TrainingStepAttemptService;
use App\Training\Service\TrainingStepProgressService;
use App\Training\Service\TrainingStepService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrainingStepController extends Controller
{
    private const ERROR_TRAINING_FINISHED = 'training_finished';
    private const ERROR_STEP_NOT_COMPLETED = 'previous_step_not_completed';

    private StepCheckService $stepCheckService;
    private TrainingStepAttemptService $trainingStepAttemptService;
    private TrainingStrategyFactory $trainingStrategyFactory;
    private CompletionConditionFactory $completionConditionFactory;
    private TrainingStepService $trainingStepService;
    private TrainingStepProgressService $trainingStepProgressService;

    public function __construct(
        TrainingStrategyFactory     $trainingStrategyFactory,
        TrainingStepAttemptService  $trainingStepAttemptService,
        StepCheckService            $stepCheckService,
        CompletionConditionFactory  $completionConditionFactory,
        TrainingStepService         $trainingStepService,
        TrainingStepProgressService $trainingStepProgressService,
    )
    {
        $this->trainingStrategyFactory = $trainingStrategyFactory;
        $this->trainingStepAttemptService = $trainingStepAttemptService;
        $this->stepCheckService = $stepCheckService;
        $this->completionConditionFactory = $completionConditionFactory;
        $this->trainingStepService = $trainingStepService;
        $this->trainingStepProgressService = $trainingStepProgressService;
    }

    public function show(Training $training, TrainingStep $step)
    {
        return new TrainingStepResource($step);
    }

    public function nextStep(Training $training)
    {
        if ($training->status == TrainingStatus::Completed) {
            return (new ApiResponseResource(
                [
                    'succes' => false,
                    'errors' => [self::ERROR_TRAINING_FINISHED => 'Training is finished'],
                    'message' => 'Training is finished',
                ]))->response()->setStatusCode(Response::HTTP_CONFLICT);
        }

        $lastStep = $training->lastStep();
        if ($lastStep && $lastStep->isPassedOrSkipped()) {
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

    public function attempt(Training $training, TrainingStep $step, Request $request)
    {
        if ($this->trainingStepService->isPassed($step)) {
            return new ApiResponseResource(['message' => 'Training is finished', 'data' => null, 'errors' => ['training_is_finished' => 'Training is finished']])
                ->response()
                ->setStatusCode(Response::HTTP_CONFLICT);
        }

        $attemptData = $request->all('attempt_data');

        $attempt = $this->trainingStepAttemptService->create($step->id, $attemptData);

        $completionCondition = $this->completionConditionFactory->create($training);

        if ($completionCondition->isCompleted()) {
            $training->complete();
        }

        return ApiResponseResource::make(['data' => new TrainingStepAttemptResource($attempt)]);
    }

    public function progress(TrainingStep $step)
    {
        $progress = $this->trainingStepProgressService->getProgress($step);

        return ApiResponseResource::make(['data' => new TrainingStepProgressResource($progress)]);
    }

    public function attempts(Request $request, TrainingStep $step)
    {
        if ($request->has('is_correct')) {
            $isCorrect = filter_var($request->input('is_correct'), FILTER_VALIDATE_BOOLEAN);
            $attempts = $step->attempts()->where('is_correct', $isCorrect)->get();
        } else {
            $attempts = $step->attempts()->get();
        }

        return new ApiResponseResource(['data' => TrainingStepAttemptResource::collection($attempts)]);
    }
}
