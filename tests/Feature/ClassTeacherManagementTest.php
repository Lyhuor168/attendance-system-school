<?php

namespace Tests\Feature;

use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassTeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_a_teacher_to_a_class_and_subject(): void
    {
        $class = SchoolClass::factory()->create();
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post("/classes/{$class->id}/teachers", ['teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        $response->assertRedirect("/classes/{$class->id}/teachers");
        $this->assertDatabaseHas('class_teacher', [
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
        ]);
    }

    public function test_assigning_a_teacher_grants_them_attendance_access_to_that_class(): void
    {
        $class = SchoolClass::factory()->create();
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $subject = Subject::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post("/classes/{$class->id}/teachers", ['teacher_id' => $teacher->id, 'subject_id' => $subject->id]);

        $this->actingAs($teacherUser)->get("/attendance/{$class->id}/record")->assertStatus(200);
    }

    public function test_duplicate_assignment_fails_validation(): void
    {
        $assignment = ClassTeacher::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->post(
            "/classes/{$assignment->school_class_id}/teachers",
            ['teacher_id' => $assignment->teacher_id, 'subject_id' => $assignment->subject_id]
        );

        $response->assertSessionHasErrors('teacher_id');
    }

    public function test_admin_can_remove_an_assignment(): void
    {
        $assignment = ClassTeacher::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/class-teachers/{$assignment->id}");

        $response->assertRedirect("/classes/{$assignment->school_class_id}/teachers");
        $this->assertDatabaseMissing('class_teacher', ['id' => $assignment->id]);
    }

    public function test_teachers_cannot_manage_class_teacher_assignments(): void
    {
        $class = SchoolClass::factory()->create();
        $teacherUser = User::factory()->teacher()->create();
        Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $this->actingAs($teacherUser)->get("/classes/{$class->id}/teachers")->assertStatus(403);
    }
}
