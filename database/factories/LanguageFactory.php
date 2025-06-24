<?php

namespace Database\Factories;

use App\Domain\Language\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->languageCode(),
            'icon' => $this->faker->imageUrl(),
        ];
    }
}
