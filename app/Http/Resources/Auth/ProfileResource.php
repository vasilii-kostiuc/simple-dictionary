<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Dictionary\DictionaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Profile',
    description: 'Profile resource',
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            description: 'User name',
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            description: 'User email address',
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'avatar',
            type: 'string',
            nullable: true,
            description: 'User avatar URL',
            example: 'https://example.com/avatars/user.jpg'
        ),
        new OA\Property(
            property: 'current_dictionary',
            type: 'integer',
            format: 'int64',
            nullable: true,
            description: 'ID of current active dictionary',
            example: 1
        )
    ]
)]
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
