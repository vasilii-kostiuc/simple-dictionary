<?php

namespace App\Http\Resources\Match;

use App\Core\Match\Models\MatchLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchLink',
    description: 'Match link resource',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '01JRH4X4TKC5ATZ78QZN4M3C0Y'),
        new OA\Property(property: 'url', type: 'string', example: 'https://example.com/api/v1/match-links/01JRH4X4TKC5ATZ78QZN4M3C0Y'),
        new OA\Property(property: 'qr_svg', type: 'string', example: '<svg>...</svg>'),
        new OA\Property(property: 'participants_limit', type: 'integer', example: 2),
        new OA\Property(property: 'status', type: 'string', example: 'active'),
        new OA\Property(property: 'payload', type: 'object'),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]

class MatchLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MatchLink $matchLink */
        $matchLink = $this->resource;

        return [
            'token' => $matchLink->token,
            'url' => route('match-links.show', ['matchLink' => $matchLink]),
            'qr_svg' => $matchLink->qr_svg,
            'participants_limit' => $matchLink->participants_limit,
            'status' => $matchLink->resolvedStatus(),
            'payload' => $matchLink->payload,
            'expires_at' => $matchLink->expires_at,
            'created_at' => $matchLink->created_at,
        ];
    }
}
