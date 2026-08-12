<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'student_number' => fake()->unique()->numerify('STU####'),
            'school_class_id' => SchoolClass::factory(),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-5 years'),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->phoneNumber(),
        ];
    }
}
