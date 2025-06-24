<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Dictionary\DictionaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => env('APP_URL').$this->avatar,
            'current_dictionary' => new DictionaryResource($this->currentDictionary),
        ];
    }
}
