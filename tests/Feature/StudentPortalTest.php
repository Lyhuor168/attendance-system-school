<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\PaymentStatus;
use App\Models\AttendanceRecord;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentWithPortal(): array
    {
        $class = SchoolClass::factory()->create();
        $user = User::factory()->student()->create();
        $student = Student::factory()->create(['school_class_id' => $class->id, 'user_id' => $user->id]);

        return [$user, $student];
    }

    public function test_student_can_log_in_and_view_dashboard(): void
    {
        [$user] = $this->makeStudentWithPortal();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('My Attendance');
        $response->assertSeeText('My Payments');
    }

    public function test_student_dashboard_only_shows_their_own_data(): void
    {
        [$user, $student] = $this->makeStudentWithPortal();
        $otherStudent = Student::factory()->create(['school_class_id' => $student->school_class_id]);

        AttendanceRecord::factory()->create([
            'student_id' => $student->id,
            'school_class_id' => $student->school_class_id,
            'date' => today(),
            'status' => AttendanceStatus::Present,
        ]);
        Payment::factory()->create(['student_id' => $student->id, 'amount' => 123.45, 'status' => PaymentStatus::Paid]);
        Payment::factory()->create(['student_id' => $otherStudent->id, 'amount' => 999.99, 'status' => PaymentStatus::Paid]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSeeText('123.45');
        $response->assertDontSeeText('999.99');
    }

    public function test_student_without_linked_profile_sees_empty_state_not_an_error(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('No student profile is linked to your account yet.');
    }

    public function test_student_is_blocked_from_admin_only_routes(): void
    {
        [$user] = $this->makeStudentWithPortal();

        $this->actingAs($user)->get('/students')->assertStatus(403);
        $this->actingAs($user)->get('/teachers')->assertStatus(403);
        $this->actingAs($user)->get('/subjects')->assertStatus(403);
        $this->actingAs($user)->get('/classes')->assertStatus(403);
        $this->actingAs($user)->get('/timetables')->assertStatus(403);
        $this->actingAs($user)->get('/payments')->assertStatus(403);
    }

    public function test_student_cannot_record_attendance_for_any_class(): void
    {
        [$user, $student] = $this->makeStudentWithPortal();

        $this->actingAs($user)->get("/attendance/{$student->school_class_id}/record")->assertStatus(403);
    }

    public function test_admin_can_create_a_student_with_optional_portal_login(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->post('/students', [
            'name' => 'Portal Student',
            'student_number' => 'STU5000',
            'school_class_id' => $class->id,
            'email' => 'portalstudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/students');

        $student = Student::where('student_number', 'STU5000')->first();
        $this->assertNotNull($student->user_id);
        $this->assertTrue($student->user->isStudent());
        $this->assertTrue($student->hasPortalAccess());
    }

    public function test_admin_can_create_a_student_without_portal_login(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->post('/students', [
            'name' => 'No Login Student',
            'student_number' => 'STU5001',
            'school_class_id' => $class->id,
        ]);

        $response->assertRedirect('/students');

        $student = Student::where('student_number', 'STU5001')->first();
        $this->assertNull($student->user_id);
        $this->assertFalse($student->hasPortalAccess());
    }

    public function test_deleting_a_student_also_deletes_their_linked_user(): void
    {
        [, $student] = $this->makeStudentWithPortal();
        $userId = $student->user_id;

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/students/{$student->id}");

        $response->assertRedirect('/students');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
