<?php

namespace App\Domain\Dictionary\Services;

use App\Domain\Dictionary\Models\Dictionary;
use App\Models\User;

class DictionaryService
{
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

        return $dictionary;
    }

    public function delete(Dictionary $dictionary): void
    {
        $dictionary->delete();
    }
}
