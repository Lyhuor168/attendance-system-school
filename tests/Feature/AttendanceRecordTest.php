<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceRecordTest extends TestCase
{
    use RefreshDatabase;

    private function makeTeacherWithClass(): array
    {
        $user = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $class = SchoolClass::factory()->create(['homeroom_teacher_id' => $teacher->id]);

        return [$user, $teacher, $class];
    }

    public function test_teacher_can_record_attendance_for_their_own_class(): void
    {
        [$user, , $class] = $this->makeTeacherWithClass();
        $students = Student::factory(3)->create(['school_class_id' => $class->id]);

        $response = $this->actingAs($user)->post("/attendance/{$class->id}", [
            'date' => today()->toDateString(),
            'attendance' => $students->map(fn (Student $s) => [
                'student_id' => $s->id,
                'status' => AttendanceStatus::Present->value,
                'remarks' => null,
            ])->all(),
        ]);

        $response->assertRedirect('/attendance');
        foreach ($students as $student) {
            $this->assertDatabaseHas('attendance_records', [
                'student_id' => $student->id,
                'school_class_id' => $class->id,
                'status' => AttendanceStatus::Present->value,
            ]);
        }
    }

    public function test_resubmitting_attendance_for_the_same_date_updates_not_duplicates(): void
    {
        [$user, , $class] = $this->makeTeacherWithClass();
        $student = Student::factory()->create(['school_class_id' => $class->id]);

        $payload = [
            'date' => today()->toDateString(),
            'attendance' => [
                ['student_id' => $student->id, 'status' => AttendanceStatus::Present->value, 'remarks' => null],
            ],
        ];

        $this->actingAs($user)->post("/attendance/{$class->id}", $payload);

        $payload['attendance'][0]['status'] = AttendanceStatus::Absent->value;
        $this->actingAs($user)->post("/attendance/{$class->id}", $payload);

        $this->assertDatabaseCount('attendance_records', 1);
        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'status' => AttendanceStatus::Absent->value,
        ]);
    }

    public function test_teacher_cannot_record_attendance_for_another_teachers_class(): void
    {
        [$user] = $this->makeTeacherWithClass();
        [, , $otherClass] = $this->makeTeacherWithClass();
        $student = Student::factory()->create(['school_class_id' => $otherClass->id]);

        $this->actingAs($user)->get("/attendance/{$otherClass->id}/record")->assertStatus(403);

        $response = $this->actingAs($user)->post("/attendance/{$otherClass->id}", [
            'date' => today()->toDateString(),
            'attendance' => [
                ['student_id' => $student->id, 'status' => AttendanceStatus::Present->value, 'remarks' => null],
            ],
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('attendance_records', ['student_id' => $student->id]);
    }

    public function test_admin_can_record_and_view_attendance_for_any_class(): void
    {
        [, , $class] = $this->makeTeacherWithClass();
        $student = Student::factory()->create(['school_class_id' => $class->id]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get("/attendance/{$class->id}/record")->assertStatus(200);

        $response = $this->actingAs($admin)->post("/attendance/{$class->id}", [
            'date' => today()->toDateString(),
            'attendance' => [
                ['student_id' => $student->id, 'status' => AttendanceStatus::Late->value, 'remarks' => 'traffic'],
            ],
        ]);

        $response->assertRedirect('/attendance');

        $viewResponse = $this->actingAs($admin)->get("/attendance/{$class->id}/".today()->toDateString());
        $viewResponse->assertStatus(200);
        $viewResponse->assertSeeText('Late');
    }
}
