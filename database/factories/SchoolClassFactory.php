<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Grade '.fake()->numberBetween(1, 6).' - '.fake()->unique()->randomLetter(),
            'grade_level' => (string) fake()->numberBetween(1, 6),
        ];
    }
}
