<?php

namespace App\Core\Match\Actions;

use App\Core\Match\Models\MatchModel;
use App\Core\Match\Services\MatchService;

class CreateMatchAction
{
    public function __construct(
        private readonly MatchService $matchService,
        private readonly StartMatchAction $startMatchAction
    ) {
    }

    public function handle(array $data, array $participants): MatchModel
    {
        $match = $this->matchService->create($data, $participants);

        return $this->startMatchAction->handle($match);
    }
}
