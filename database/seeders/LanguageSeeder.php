<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('languages')->upsert([
            [
                'id' => 1,
                'name' => 'Русский',
                'code' => 'ru',
                'icon' => '/img/lang/ru.svg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'English',
                'code' => 'en',
                'icon' => '/img/lang/gb.svg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Deutsch',
                'code' => 'de',
                'icon' => '/img/lang/de.svg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['name', 'code', 'icon', 'updated_at']);
    }
}
