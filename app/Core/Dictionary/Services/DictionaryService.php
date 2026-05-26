<?php

namespace App\Core\Dictionary\Services;

use App\Core\Dictionary\Models\Dictionary;
use App\Core\Shared\Cache\CacheInterface;
use App\Core\User\Models\User;

class DictionaryService
{
    public function __construct(private readonly CacheInterface $cache) {}

    public function forUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->cache->remember(
            "user.{$userId}.dictionaries",
            3600,
            fn () => Dictionary::query()->where('user_id', $userId)->get()
        );
    }

    public function create(array $data): Dictionary
    {
        $dictionary = Dictionary::create([
            'user_id' => $data['user_id'],
            'language_from_id' => $data['language_from_id'],
            'language_to_id' => $data['language_to_id'],
        ]);
        $user = User::find($data['user_id']);

        if ($user->dictionaries()->count() === 1) {
            $user->update([
                'current_dictionary' => $dictionary->id,
            ]);
        }

        $user->refresh();

        $this->cache->forget("user.{$data['user_id']}.dictionaries");

        return $dictionary;
    }

    public function delete(Dictionary $dictionary): void
    {
        $userId = $dictionary->user_id;
        $dictionary->delete();
        $this->cache->forget("user.{$userId}.dictionaries");
    }
}
