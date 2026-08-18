<?php

namespace Database\Factories;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveRequest;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'student_id' => Student::factory(),
            'reason' => fake()->randomElement([
                'Family trip', 'Medical appointment', 'Feeling unwell', 'Religious observance',
            ]),
            'from_date' => $from,
            'to_date' => (clone $from)->modify('+'.fake()->numberBetween(0, 2).' days'),
            'status' => LeaveRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => LeaveRequestStatus::Approved]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => ['status' => LeaveRequestStatus::Rejected]);
    }
}
