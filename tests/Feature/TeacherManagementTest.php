<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_teacher_list(): void
    {
        Teacher::factory(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/teachers');

        $response->assertStatus(200);
    }

    public function test_admins_can_create_a_teacher_with_a_linked_user_account(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/teachers', [
            'name' => 'Jane Teacher',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'employee_number' => 'EMP1234',
            'phone' => '555-0100',
        ]);

        $response->assertRedirect('/teachers');

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->role === Role::Teacher);
        $this->assertDatabaseHas('teachers', ['employee_number' => 'EMP1234', 'user_id' => $user->id]);
    }

    public function test_admins_can_update_a_teacher(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/teachers/{$teacher->id}", [
            'name' => 'Updated Name',
            'email' => $teacherUser->email,
            'employee_number' => $teacher->employee_number,
        ]);

        $response->assertRedirect('/teachers');
        $this->assertDatabaseHas('users', ['id' => $teacherUser->id, 'name' => 'Updated Name']);
    }

    public function test_deleting_a_teacher_also_deletes_the_linked_user(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/teachers/{$teacher->id}");

        $response->assertRedirect('/teachers');
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('users', ['id' => $teacherUser->id]);
    }

    public function test_teachers_cannot_manage_teachers(): void
    {
        $teacher = Teacher::factory()->create();
        $actingTeacher = User::factory()->teacher()->create();

        $this->actingAs($actingTeacher)->get('/teachers')->assertStatus(403);
        $this->actingAs($actingTeacher)->delete("/teachers/{$teacher->id}")->assertStatus(403);
    }
}
