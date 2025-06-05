<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dictionaries')->insert([
            'language_from_id' => 1,
            'language_to_id' => 2,
            'user_id' => 1,
        ]);

        DB::table('dictionaries')->insert([
            'language_from_id' => 2,
            'language_to_id' => 3,
            'user_id' => 1,
        ]);

        DB::table('dictionaries')->insert([
            'language_from_id' => 1,
            'language_to_id' => 3,
            'user_id' => 1,
        ]);
    }
}
