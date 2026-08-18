<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    private function makeLinkedTrio(): array
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $class = SchoolClass::factory()->create(['homeroom_teacher_id' => $teacher->id]);

        $guardianUser = User::factory()->create(['role' => Role::Guardian]);
        $guardian = Guardian::factory()->create(['user_id' => $guardianUser->id]);

        $student = Student::factory()->create(['school_class_id' => $class->id, 'guardian_id' => $guardian->id]);

        return [$teacherUser, $guardianUser, $student, $class];
    }

    public function test_teacher_can_message_the_guardian_of_their_own_student(): void
    {
        [$teacherUser, $guardianUser, $student] = $this->makeLinkedTrio();

        $response = $this->actingAs($teacherUser)->post("/chat/{$student->id}", ['message' => 'Hello guardian']);

        $response->assertRedirect("/chat/{$student->id}");
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $teacherUser->id,
            'receiver_id' => $guardianUser->id,
            'student_id' => $student->id,
            'message' => 'Hello guardian',
        ]);
    }

    public function test_guardian_can_message_the_teacher_of_their_child(): void
    {
        [$teacherUser, $guardianUser, $student] = $this->makeLinkedTrio();

        $response = $this->actingAs($guardianUser)->post("/chat/{$student->id}", ['message' => 'How is my child doing?']);

        $response->assertRedirect("/chat/{$student->id}");
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $guardianUser->id,
            'receiver_id' => $teacherUser->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_teacher_cannot_message_about_a_student_outside_their_assigned_classes(): void
    {
        [, , $student] = $this->makeLinkedTrio();
        $otherTeacherUser = User::factory()->teacher()->create();
        Teacher::factory()->create(['user_id' => $otherTeacherUser->id]);

        $response = $this->actingAs($otherTeacherUser)->post("/chat/{$student->id}", ['message' => 'Unauthorized']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('chat_messages', ['message' => 'Unauthorized']);
    }

    public function test_guardian_cannot_message_about_a_student_who_is_not_their_child(): void
    {
        [, , $student] = $this->makeLinkedTrio();
        $otherGuardianUser = User::factory()->create(['role' => Role::Guardian]);
        Guardian::factory()->create(['user_id' => $otherGuardianUser->id]);

        $response = $this->actingAs($otherGuardianUser)->post("/chat/{$student->id}", ['message' => 'Not my child']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('chat_messages', ['message' => 'Not my child']);
    }

    public function test_teacher_sees_only_students_with_a_guardian_in_their_assigned_classes(): void
    {
        [$teacherUser, , $student] = $this->makeLinkedTrio();
        [, , $otherStudent] = $this->makeLinkedTrio();

        $response = $this->actingAs($teacherUser)->get('/chat');

        $response->assertSeeText($student->name);
        $response->assertDontSeeText($otherStudent->name);
    }

    public function test_guardian_sees_only_their_own_children(): void
    {
        [, $guardianUser, $student] = $this->makeLinkedTrio();
        [, , $otherStudent] = $this->makeLinkedTrio();

        $response = $this->actingAs($guardianUser)->get('/chat');

        $response->assertSeeText($student->name);
        $response->assertDontSeeText($otherStudent->name);
    }

    public function test_viewing_a_thread_marks_messages_as_read(): void
    {
        [$teacherUser, $guardianUser, $student] = $this->makeLinkedTrio();

        $this->actingAs($guardianUser)->post("/chat/{$student->id}", ['message' => 'Question for teacher']);

        $this->assertDatabaseHas('chat_messages', ['receiver_id' => $teacherUser->id, 'is_read' => false]);

        $this->actingAs($teacherUser)->get("/chat/{$student->id}");

        $this->assertDatabaseHas('chat_messages', ['receiver_id' => $teacherUser->id, 'is_read' => true]);
    }

    public function test_admin_cannot_access_chat(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/chat')->assertStatus(403);
    }

    public function test_student_cannot_access_chat(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get('/chat')->assertStatus(403);
    }
}
