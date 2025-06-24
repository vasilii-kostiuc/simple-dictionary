<?php

namespace App\Http\Resources\Dictionary;

use App\Http\Resources\Language\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
