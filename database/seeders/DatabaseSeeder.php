<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Тестовые пользователи только для локальной разработки
        if (app()->environment('local')) {
            // User::factory(10)->create();

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Дефолтные данные для всех окружений
        $this->call(LanguageSeeder::class);
        $this->call(TopWordSeeder::class);
    }
}
