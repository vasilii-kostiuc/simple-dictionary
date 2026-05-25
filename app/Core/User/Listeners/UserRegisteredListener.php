<?php

namespace App\Core\User\Listeners;

use App\Core\Dictionary\Services\DictionaryService;
use Illuminate\Auth\Events\Registered;

class UserRegisteredListener
{
    public function __construct(
        private readonly DictionaryService $dictionaryService
    ) {}

    public function handle(Registered $event): void
    {
        $this->dictionaryService->create([
            'user_id' => $event->user->id,
            'language_from_id' => 2,
            'language_to_id' => 1,
        ]);
    }
}
