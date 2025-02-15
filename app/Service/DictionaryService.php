<?php

namespace App\Service;

use App\Models\Dictionary;

class DictionaryService
{
    public function create(array $data ): Dictionary
    {
        $dictionary = Dictionary::create([
            'user_id' => $data['user_id'],
            'language_from_id' => $data['language_from_id'],
            'language_to_id' => $data['language_to_id'],
        ]);

        return $dictionary;
    }

    public function delete(Dictionary $dictionary): void
    {
        $dictionary->delete();
    }
}
