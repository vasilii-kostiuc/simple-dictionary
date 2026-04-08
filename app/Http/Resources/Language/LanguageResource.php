<?php

namespace App\Http\Resources\Language;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Language',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'English'),
        new OA\Property(property: 'code', type: 'string', example: 'en'),
        new OA\Property(property: 'icon', type: 'string', example: 'http://example.com/storage/flags/en.svg'),
    ]
)]
class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'icon' => asset($this->icon),
        ];
    }
}
