<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'status' => PaymentStatus::Paid,
            'paid_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'reference' => fake()->unique()->bothify('PMT-####??'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PaymentStatus::Paid]);
    }

    public function partial(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PaymentStatus::Partial]);
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Unpaid,
            'amount' => 0,
        ]);
    }
}
