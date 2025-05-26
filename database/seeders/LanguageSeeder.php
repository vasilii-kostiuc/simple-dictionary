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
        DB::table('languages')->truncate();

        DB::table('languages')->insert([
            'id' => 1,
            'name' => 'Русский',
            'code' => 'ru',
            'icon' => '/img/lang/ru.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]
        );

        DB::table('languages')->insert([
            'id' => 2,
            'name' => 'English',
            'code' => 'en',
            'icon' => '/img/lang/gb.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]
        );

        DB::table('languages')->insert([
            'id' => 3,
            'name' => 'Deutsch',
            'code' => 'de',
            'icon' => '/img/lang/de.svg',
            'created_at' => now(),
            'updated_at' => now(),
        ]
        );
    }
}
