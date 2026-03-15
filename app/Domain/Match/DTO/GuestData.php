<?php

namespace App\Domain\Match\DTO;

readonly class GuestData
{
    public function __construct(
        public string $guestId,
        public ?string $name = null,
    ) {}

    public function toArray(): array
    {
        return [
            'guest_id' => $this->guestId,
            'name' => $this->name,
        ];
    }
}
