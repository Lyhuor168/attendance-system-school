<?php

namespace Database\Factories;

use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassTeacher>
 */
class ClassTeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'school_class_id' => SchoolClass::factory(),
            'subject_id' => Subject::factory(),
        ];
    }
}
