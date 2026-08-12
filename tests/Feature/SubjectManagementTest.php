<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_subject_list(): void
    {
        Subject::factory(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/subjects');

        $response->assertStatus(200);
    }

    public function test_admins_can_create_a_subject(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/subjects', [
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $response->assertRedirect('/subjects');
        $this->assertDatabaseHas('subjects', ['name' => 'Mathematics', 'code' => 'MATH101']);
    }

    public function test_admins_can_update_a_subject(): void
    {
        $subject = Subject::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->put("/subjects/{$subject->id}", [
            'name' => 'Updated Name',
            'code' => $subject->code,
        ]);

        $response->assertRedirect('/subjects');
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Updated Name']);
    }

    public function test_admins_can_delete_a_subject(): void
    {
        $subject = Subject::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/subjects/{$subject->id}");

        $response->assertRedirect('/subjects');
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_teachers_cannot_manage_subjects(): void
    {
        $subject = Subject::factory()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/subjects')->assertStatus(403);
        $this->actingAs($teacher)->post('/subjects', ['name' => 'x', 'code' => 'y'])->assertStatus(403);
        $this->actingAs($teacher)->put("/subjects/{$subject->id}", ['name' => 'x', 'code' => 'y'])->assertStatus(403);
        $this->actingAs($teacher)->delete("/subjects/{$subject->id}")->assertStatus(403);
    }
}
