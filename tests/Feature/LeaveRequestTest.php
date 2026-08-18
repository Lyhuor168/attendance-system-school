<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveRequest;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentWithClass(): array
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $class = SchoolClass::factory()->create(['homeroom_teacher_id' => $teacher->id]);
        $studentUser = User::factory()->student()->create();
        $student = Student::factory()->create(['school_class_id' => $class->id, 'user_id' => $studentUser->id]);

        return [$studentUser, $student, $teacherUser, $teacher, $class];
    }

    public function test_student_can_submit_a_leave_request_for_themselves(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();

        $response = $this->actingAs($studentUser)->post('/leave-requests', [
            'reason' => 'Family trip',
            'from_date' => today()->addDays(3)->toDateString(),
            'to_date' => today()->addDays(5)->toDateString(),
        ]);

        $response->assertRedirect('/leave-requests');
        $this->assertDatabaseHas('leave_requests', [
            'student_id' => $student->id,
            'reason' => 'Family trip',
            'status' => LeaveRequestStatus::Pending->value,
        ]);
    }

    public function test_a_malicious_student_id_in_the_request_is_ignored(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();
        $otherStudent = Student::factory()->create();

        $this->actingAs($studentUser)->post('/leave-requests', [
            'student_id' => $otherStudent->id,
            'reason' => 'Spoofed request',
            'from_date' => today()->addDays(1)->toDateString(),
            'to_date' => today()->addDays(1)->toDateString(),
        ]);

        $this->assertDatabaseHas('leave_requests', ['reason' => 'Spoofed request', 'student_id' => $student->id]);
        $this->assertDatabaseMissing('leave_requests', ['reason' => 'Spoofed request', 'student_id' => $otherStudent->id]);
    }

    public function test_student_sees_only_their_own_leave_requests(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();
        $ownRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        [, $otherStudent] = $this->makeStudentWithClass();
        LeaveRequest::factory()->create(['student_id' => $otherStudent->id, 'school_class_id' => $otherStudent->school_class_id]);

        $response = $this->actingAs($studentUser)->get('/leave-requests');

        $response->assertSeeText($ownRequest->reason);
        $this->assertEquals(1, $response->viewData('leaveRequests')->total());
    }

    public function test_student_cannot_approve_or_reject_requests(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        $response = $this->actingAs($studentUser)->post("/leave-requests/{$leaveRequest->id}/respond", ['status' => 'approved']);

        $response->assertStatus(403);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id, 'status' => LeaveRequestStatus::Pending->value]);
    }

    public function test_teacher_can_approve_a_leave_request_from_their_assigned_class(): void
    {
        [, $student, $teacherUser] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        $response = $this->actingAs($teacherUser)->post("/leave-requests/{$leaveRequest->id}/respond", ['status' => 'approved']);

        $response->assertRedirect('/leave-requests');
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => LeaveRequestStatus::Approved->value,
            'reviewed_by' => $teacherUser->id,
        ]);
    }

    public function test_teacher_cannot_respond_to_a_leave_request_from_another_class(): void
    {
        [, $student] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        [, , $otherTeacherUser] = $this->makeStudentWithClass();

        $response = $this->actingAs($otherTeacherUser)->post("/leave-requests/{$leaveRequest->id}/respond", ['status' => 'approved']);

        $response->assertStatus(403);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id, 'status' => LeaveRequestStatus::Pending->value]);
    }

    public function test_teacher_only_sees_leave_requests_for_their_assigned_classes(): void
    {
        [, $student, $teacherUser] = $this->makeStudentWithClass();
        $ownClassRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        [, $otherStudent] = $this->makeStudentWithClass();
        LeaveRequest::factory()->create(['student_id' => $otherStudent->id, 'school_class_id' => $otherStudent->school_class_id]);

        $response = $this->actingAs($teacherUser)->get('/leave-requests');

        $response->assertSeeText($ownClassRequest->reason);
        $this->assertEquals(1, $response->viewData('leaveRequests')->total());
    }

    public function test_admin_sees_all_leave_requests(): void
    {
        [, $student1] = $this->makeStudentWithClass();
        [, $student2] = $this->makeStudentWithClass();
        LeaveRequest::factory()->create(['student_id' => $student1->id, 'school_class_id' => $student1->school_class_id]);
        LeaveRequest::factory()->create(['student_id' => $student2->id, 'school_class_id' => $student2->school_class_id]);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/leave-requests');

        $this->assertEquals(2, $response->viewData('leaveRequests')->total());
    }

    public function test_student_can_cancel_their_own_pending_request(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        $response = $this->actingAs($studentUser)->delete("/leave-requests/{$leaveRequest->id}");

        $response->assertRedirect('/leave-requests');
        $this->assertDatabaseMissing('leave_requests', ['id' => $leaveRequest->id]);
    }

    public function test_student_cannot_cancel_another_students_request(): void
    {
        [$studentUser] = $this->makeStudentWithClass();
        [, $otherStudent] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->create(['student_id' => $otherStudent->id, 'school_class_id' => $otherStudent->school_class_id]);

        $response = $this->actingAs($studentUser)->delete("/leave-requests/{$leaveRequest->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id]);
    }

    public function test_student_cannot_cancel_an_already_approved_request(): void
    {
        [$studentUser, $student] = $this->makeStudentWithClass();
        $leaveRequest = LeaveRequest::factory()->approved()->create(['student_id' => $student->id, 'school_class_id' => $student->school_class_id]);

        $response = $this->actingAs($studentUser)->delete("/leave-requests/{$leaveRequest->id}");

        $response->assertStatus(403);
    }
}
