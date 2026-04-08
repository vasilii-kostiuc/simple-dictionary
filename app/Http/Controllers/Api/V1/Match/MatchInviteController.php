<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Domain\Match\Actions\CreateMatchInviteAction;
use App\Domain\Match\Models\MatchInvite;
use App\Domain\Match\Services\MatchInviteQrCodeGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\StoreMatchInviteRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Match\MatchInviteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MatchInviteController extends Controller
{
    public function __construct(
        private readonly CreateMatchInviteAction $createMatchInviteAction,
        private readonly MatchInviteQrCodeGenerator $matchInviteQrCodeGenerator
    ) {
    }

    public function store(StoreMatchInviteRequest $request): JsonResponse
    {
        $invite = $this->createMatchInviteAction->handle($request->validated(), $request->user());
        $this->appendPresentationData($invite);

        return new ApiResponseResource([
            'data' => new MatchInviteResource($invite),
            'message' => 'Match invite created successfully',
        ])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MatchInvite $matchInvite): JsonResponse
    {
        $this->appendPresentationData($matchInvite);

        return new ApiResponseResource([
            'data' => new MatchInviteResource($matchInvite),
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    private function appendPresentationData(MatchInvite $invite): void
    {
        $url = route('match-links.show', ['matchInvite' => $invite]);

        $invite->setAttribute('qr_svg', $this->matchInviteQrCodeGenerator->generate($url));
    }
}
