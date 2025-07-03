<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    description: 'User resource',
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            format: 'int64',
            description: 'User ID',
            example: 1
        ),
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
class UserResource extends JsonResource
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
            'email' => $this->email,
        ];
    }
}
