<?php

namespace App\Http\Resources\Dictionary;

use App\Http\Resources\Language\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Dictionary',
    description: 'Dictionary resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'language_from_id', type: 'integer', example: 1),
        new OA\Property(property: 'language_from', ref: '#/components/schemas/Language', type: 'object'),
        new OA\Property(property: 'language_to_id', type: 'integer', example: 2),
        new OA\Property(property: 'language_to', ref: '#/components/schemas/Language', type: 'object'),
    ]
)]
class DictionaryResource extends JsonResource
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
            'language_from_id' => $this->language_from_id,
            'language_from' => LanguageResource::make($this->languageFrom),
            'language_to_id' => $this->language_to_id,
            'language_to' => LanguageResource::make($this->languageTo),
        ];
    }
}
