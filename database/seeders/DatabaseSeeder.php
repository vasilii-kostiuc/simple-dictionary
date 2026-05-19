<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
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

            User::query()->firstOrCreate(
                ['email' => 'test@example.com'],
                User::factory()->make(['name' => 'Test User'])->toArray()
            );
        }

        // Дефолтные данные для всех окружений
        $this->call(LanguageSeeder::class);
        $this->call(TopWordSeeder::class);
    }
}
