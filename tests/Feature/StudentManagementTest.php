<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_student_list(): void
    {
        Student::factory(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/students');

        $response->assertStatus(200);
    }

    public function test_admins_can_create_a_student(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->post('/students', [
            'name' => 'John Student',
            'student_number' => 'STU1234',
            'school_class_id' => $class->id,
        ]);

        $response->assertRedirect('/students');
        $this->assertDatabaseHas('students', ['student_number' => 'STU1234', 'school_class_id' => $class->id]);
    }

    public function test_admins_can_update_a_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->put("/students/{$student->id}", [
            'name' => 'Renamed Student',
            'student_number' => $student->student_number,
            'school_class_id' => $student->school_class_id,
        ]);

        $response->assertRedirect('/students');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Renamed Student']);
    }

    public function test_admins_can_delete_a_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/students/{$student->id}");

        $response->assertRedirect('/students');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_teachers_cannot_manage_students(): void
    {
        $student = Student::factory()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/students')->assertStatus(403);
        $this->actingAs($teacher)->delete("/students/{$student->id}")->assertStatus(403);
    }
}
