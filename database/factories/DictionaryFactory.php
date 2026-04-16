<?php

namespace Database\Factories;

use App\Domain\Dictionary\Models\Dictionary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Dictionary\Models\Dictionary>
 */
class DictionaryFactory extends Factory
{
    protected $model = Dictionary::class;

    public function definition(): array
    {
        return [];
    }
}
