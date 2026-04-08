<?php

namespace App\Domain\Match\Actions;

use App\Domain\Match\Enums\MatchCompletionReason;
use App\Domain\Match\Models\MatchModel;
use App\Domain\Match\Services\MatchService;

class CompleteMatchAction
{
    public function __construct(
        private readonly MatchService $matchService
    ) {
    }

    public function handle(MatchModel $match, ?MatchCompletionReason $reason = null, ?array $details = []): MatchModel
    {
        return $this->matchService->complete($match, $reason, $details);
    }
}
