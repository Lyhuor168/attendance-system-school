<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
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
            'school_class_id' => SchoolClass::factory(),
            'date' => today(),
            'status' => AttendanceStatus::Present,
            'remarks' => null,
        ];
    }

    public function present(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AttendanceStatus::Present]);
    }

    public function absent(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AttendanceStatus::Absent]);
    }

    public function late(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AttendanceStatus::Late]);
    }

    public function excused(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AttendanceStatus::Excused]);
    }
}
