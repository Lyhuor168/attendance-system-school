<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
        ]);

        Subject::factory(6)->create();

        $teachers = User::factory(4)->teacher()->create()
            ->map(fn (User $user) => Teacher::factory()->create(['user_id' => $user->id]));

        $classes = $teachers->map(fn (Teacher $teacher) => SchoolClass::factory()->create([
            'homeroom_teacher_id' => $teacher->id,
        ]));

        $classes->each(function (SchoolClass $class): void {
            $students = Student::factory(15)->create(['school_class_id' => $class->id]);

            $days = collect(DayOfWeek::cases())->take(5);

            $days->each(fn (DayOfWeek $day, int $index) => Timetable::factory()->create([
                'school_class_id' => $class->id,
                'subject_id' => Subject::inRandomOrder()->first()->id,
                'teacher_id' => $class->homeroom_teacher_id,
                'day_of_week' => $day->value,
                'start_time' => sprintf('%02d:00', 8 + $index),
                'end_time' => sprintf('%02d:00', 9 + $index),
            ]));

            $students->each(fn (Student $student) => AttendanceRecord::factory()->create([
                'student_id' => $student->id,
                'school_class_id' => $class->id,
                'recorded_by' => $class->homeroomTeacher->user_id,
                'status' => fake()->randomElement(AttendanceStatus::cases()),
            ]));
        });
    }
}
