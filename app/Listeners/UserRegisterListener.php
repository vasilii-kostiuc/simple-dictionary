<?php

namespace App\Listeners;

use App\Domain\Dictionary\Models\Dictionary;
use App\Domain\Dictionary\Services\DictionaryService;
use App\Domain\Language\Models\Language;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;

class UserRegisterListener
{
    private DictionaryService $dictionaryService;

    /**
     * Create the event listener.
     */
    public function __construct(DictionaryService $dictionaryService)
    {
        $this->dictionaryService = $dictionaryService;
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $data = [
            'user_id' => $event->user->id,
            'language_from_id' => 2,
            'language_to_id' => 1,
        ];

        $this->dictionaryService->create($data);
    }
}
