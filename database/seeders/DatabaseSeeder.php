<?php

namespace Database\Seeders;

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Models\AttendanceRecord;
use App\Models\ClassTeacher;
use App\Models\Guardian;
use App\Models\LeaveRequest;
use App\Models\Payment;
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

        // Cross-assign each teacher to the next class (circular) for a
        // second subject, demonstrating the class_teacher pivot scope in
        // addition to homeroom-only assignment.
        $classes->each(function (SchoolClass $class, int $index) use ($teachers): void {
            $nextTeacher = $teachers[($index + 1) % $teachers->count()];

            ClassTeacher::create([
                'teacher_id' => $nextTeacher->id,
                'school_class_id' => $class->id,
                'subject_id' => Subject::inRandomOrder()->first()->id,
            ]);
        });

        $classes->each(function (SchoolClass $class): void {
            $students = Student::factory(15)->create(['school_class_id' => $class->id]);

            $portalStudent = $students->first();
            $portalUser = User::factory()->student()->create(['name' => $portalStudent->name]);

            $guardianUser = User::factory()->create(['role' => Role::Guardian]);
            $guardian = Guardian::factory()->create(['user_id' => $guardianUser->id]);
            $portalStudent->update(['user_id' => $portalUser->id, 'guardian_id' => $guardian->id]);

            // A couple more students get contact-only guardians (no login).
            $students->slice(1, 2)->each(fn (Student $student) => $student->update([
                'guardian_id' => Guardian::factory()->create()->id,
            ]));

            $days = collect(DayOfWeek::cases())->take(5);

            $days->each(fn (DayOfWeek $day, int $index) => Timetable::factory()->create([
                'school_class_id' => $class->id,
                'subject_id' => Subject::inRandomOrder()->first()->id,
                'teacher_id' => $class->homeroom_teacher_id,
                'day_of_week' => $day->value,
                'start_time' => sprintf('%02d:00', 8 + $index),
                'end_time' => sprintf('%02d:00', 9 + $index),
            ]));

            $students->each(function (Student $student) use ($class): void {
                for ($daysAgo = 0; $daysAgo < 7; $daysAgo++) {
                    AttendanceRecord::factory()->create([
                        'student_id' => $student->id,
                        'school_class_id' => $class->id,
                        'recorded_by' => $class->homeroomTeacher->user_id,
                        'date' => today()->subDays($daysAgo),
                        'status' => fake()->randomElement([
                            AttendanceStatus::Present,
                            AttendanceStatus::Present,
                            AttendanceStatus::Present,
                            AttendanceStatus::Absent,
                            AttendanceStatus::Late,
                            AttendanceStatus::Excused,
                        ]),
                    ]);
                }

                for ($i = 0, $count = fake()->numberBetween(1, 3); $i < $count; $i++) {
                    $status = fake()->randomElement([
                        PaymentStatus::Paid,
                        PaymentStatus::Paid,
                        PaymentStatus::Paid,
                        PaymentStatus::Partial,
                        PaymentStatus::Unpaid,
                    ]);

                    Payment::factory()->create([
                        'student_id' => $student->id,
                        'recorded_by' => $class->homeroomTeacher->user_id,
                        'status' => $status,
                        'amount' => $status === PaymentStatus::Unpaid ? 0 : fake()->randomFloat(2, 50, 500),
                    ]);
                }
            });

            $students->random(3)->each(function (Student $student) use ($class) {
                $status = fake()->randomElement(['pending', 'approved', 'rejected']);

                $factory = match ($status) {
                    'approved' => LeaveRequest::factory()->approved(),
                    'rejected' => LeaveRequest::factory()->rejected(),
                    default => LeaveRequest::factory(),
                };

                $factory->create([
                    'student_id' => $student->id,
                    'school_class_id' => $class->id,
                    'teacher_id' => $class->homeroom_teacher_id,
                    'reviewed_by' => $status === 'pending' ? null : $class->homeroomTeacher->user_id,
                ]);
            });
        });

        $this->seedYearFourItEveningClasses();
    }

    /**
     * Seed the two Year 4 IT evening sections (IT10B1, IT10B2), their 6 IT
     * subjects, and a dedicated teacher per subject (assigned to both
     * sections via the class_teacher pivot), with a Monday-Saturday
     * timetable where each day's teacher matches that day's subject.
     */
    private function seedYearFourItEveningClasses(): void
    {
        $subjects = collect([
            ['name' => 'Programming', 'code' => 'IT-PRG4'],
            ['name' => 'Network Engineering', 'code' => 'IT-NET4'],
            ['name' => 'Database System', 'code' => 'IT-DB4'],
            ['name' => 'Data Analytics', 'code' => 'IT-DA4'],
            ['name' => 'Enterprise Systems', 'code' => 'IT-ES4'],
            ['name' => 'Cyber Security', 'code' => 'IT-CS4'],
        ])->map(fn (array $attributes) => Subject::factory()->create($attributes));

        $teachers = $subjects->map(fn () => Teacher::factory()->create([
            'user_id' => User::factory()->teacher()->create()->id,
        ]));

        $days = collect(DayOfWeek::cases())->reject(fn (DayOfWeek $day) => $day === DayOfWeek::Sunday)->values();

        // The Programming and Network Engineering teachers double as
        // homeroom teacher for IT10B1 and IT10B2 respectively.
        $sections = ['IT10B1' => $teachers[0], 'IT10B2' => $teachers[1]];

        foreach ($sections as $section => $homeroomTeacher) {
            $class = SchoolClass::factory()->create([
                'name' => "{$section} - ការវិភាគទិន្នន័យឆ្នាំទី ៤",
                'grade_level' => '4',
                'homeroom_teacher_id' => $homeroomTeacher->id,
            ]);

            Student::factory(15)->create(['school_class_id' => $class->id]);

            $subjects->each(function (Subject $subject, int $index) use ($class, $teachers): void {
                ClassTeacher::create([
                    'teacher_id' => $teachers[$index]->id,
                    'school_class_id' => $class->id,
                    'subject_id' => $subject->id,
                ]);
            });

            $days->each(fn (DayOfWeek $day, int $index) => Timetable::factory()->create([
                'school_class_id' => $class->id,
                'subject_id' => $subjects[$index]->id,
                'teacher_id' => $teachers[$index]->id,
                'day_of_week' => $day->value,
                'start_time' => '17:30',
                'end_time' => '18:30',
            ]));
        }
    }
}
