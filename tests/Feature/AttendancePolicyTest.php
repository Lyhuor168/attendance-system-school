<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_any_attendance_record(): void
    {
        $admin = User::factory()->admin()->create();
        $record = AttendanceRecord::factory()->create();

        $this->assertTrue($admin->can('view', $record));
        $this->assertTrue($admin->can('update', $record));
    }

    public function test_teacher_can_view_and_update_records_for_their_homeroom_class(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $class = SchoolClass::factory()->create(['homeroom_teacher_id' => $teacher->id]);
        $record = AttendanceRecord::factory()->create(['school_class_id' => $class->id]);

        $this->assertTrue($teacherUser->can('view', $record));
        $this->assertTrue($teacherUser->can('update', $record));
    }

    public function test_teacher_can_view_and_update_records_for_a_class_teacher_pivot_assigned_class(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $class = SchoolClass::factory()->create(); // not homeroom
        ClassTeacher::factory()->create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id]);
        $record = AttendanceRecord::factory()->create(['school_class_id' => $class->id]);

        $this->assertTrue($teacherUser->can('view', $record));
        $this->assertTrue($teacherUser->can('update', $record));
    }

    public function test_teacher_cannot_view_or_update_records_outside_their_assigned_classes(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $record = AttendanceRecord::factory()->create();

        $this->assertFalse($teacherUser->can('view', $record));
        $this->assertFalse($teacherUser->can('update', $record));
    }

    public function test_student_can_view_only_their_own_attendance_record(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        $ownRecord = AttendanceRecord::factory()->create(['student_id' => $student->id]);
        $otherRecord = AttendanceRecord::factory()->create();

        $this->assertTrue($studentUser->can('view', $ownRecord));
        $this->assertFalse($studentUser->can('view', $otherRecord));
    }

    public function test_student_can_never_update_attendance_even_their_own(): void
    {
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        $ownRecord = AttendanceRecord::factory()->create(['student_id' => $student->id]);

        $this->assertFalse($studentUser->can('update', $ownRecord));
    }

    public function test_only_admin_and_teacher_can_create_attendance_records(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create();
        $studentUser = User::factory()->student()->create();

        $this->assertTrue($admin->can('create', AttendanceRecord::class));
        $this->assertTrue($teacherUser->can('create', AttendanceRecord::class));
        $this->assertFalse($studentUser->can('create', AttendanceRecord::class));
    }
}
