<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->teacher(),
            'employee_number' => fake()->unique()->numerify('EMP####'),
            'phone' => fake()->phoneNumber(),
            'hired_at' => fake()->dateTimeBetween('-10 years', '-1 month'),
        ];
    }
}
