<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Core\Match\Actions\CreateMatchLinkAction;
use App\Core\Match\Models\MatchLink;
use App\Core\Match\Services\MatchLinkQrCodeGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\StoreMatchLinkRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\Match\MatchLinkResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MatchLinkController extends Controller
{
    public function __construct(
        private readonly CreateMatchLinkAction $createMatchLinkAction,
        private readonly MatchLinkQrCodeGenerator $matchLinkQrCodeGenerator
    ) {}

    public function store(StoreMatchLinkRequest $request): JsonResponse
    {
        $matchLink = $this->createMatchLinkAction->handle($request->validated(), $request->user());
        $this->appendPresentationData($matchLink);

        return new ApiResponseResource([
            'data' => new MatchLinkResource($matchLink),
            'message' => 'Match link created successfully',
        ])->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MatchLink $matchLink): JsonResponse
    {
        $this->appendPresentationData($matchLink);

        return new ApiResponseResource([
            'data' => new MatchLinkResource($matchLink),
        ])->response()->setStatusCode(Response::HTTP_OK);
    }

    private function appendPresentationData(MatchLink $matchLink): void
    {
        $url = route('match-links.show', ['matchLink' => $matchLink]);

        $matchLink->setAttribute('qr_svg', $this->matchLinkQrCodeGenerator->generate($url));
    }
}
