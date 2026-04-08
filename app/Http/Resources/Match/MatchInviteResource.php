<?php

namespace App\Http\Resources\Match;

use App\Domain\Match\Models\MatchInvite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchInvite',
    description: 'Match invite resource',
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

class MatchInviteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MatchInvite $invite */
        $invite = $this->resource;

        return [
            'token' => $invite->token,
            'url' => route('match-links.show', ['matchInvite' => $invite]),
            'qr_svg' => $invite->qr_svg,
            'participants_limit' => $invite->participants_limit,
            'status' => $invite->resolvedStatus(),
            'payload' => $invite->payload,
            'expires_at' => $invite->expires_at,
            'created_at' => $invite->created_at,
        ];
    }
}
