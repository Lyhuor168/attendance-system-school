<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admins_see_all_module_tiles(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Teacher');
        $response->assertSeeText('Classes');
        $response->assertSeeText('Subjects');
        $response->assertSeeText('Timetable');
        $response->assertSeeText('Attendance');
    }

    public function test_teachers_see_only_attendance_and_their_class(): void
    {
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $class = SchoolClass::factory()->create(['homeroom_teacher_id' => $teacher->id]);

        $response = $this->actingAs($teacherUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Attendance');
        $response->assertSeeText($class->name);
        $response->assertDontSeeText('Add / Edit / Delete teachers');
    }
}
