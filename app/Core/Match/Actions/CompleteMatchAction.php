<?php

namespace App\Core\Match\Actions;

use App\Core\Match\Enums\MatchCompletionReason;
use App\Core\Match\Models\MatchModel;
use App\Core\Match\Services\MatchService;

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
