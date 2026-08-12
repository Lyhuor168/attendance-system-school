<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_class_list(): void
    {
        SchoolClass::factory(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/classes');

        $response->assertStatus(200);
    }

    public function test_admins_can_create_a_class(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/classes', [
            'name' => 'Grade 5 - A',
            'grade_level' => '5',
        ]);

        $response->assertRedirect('/classes');
        $this->assertDatabaseHas('school_classes', ['name' => 'Grade 5 - A']);
    }

    public function test_admins_can_update_a_class(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->put("/classes/{$class->id}", [
            'name' => 'Renamed Class',
            'grade_level' => $class->grade_level,
        ]);

        $response->assertRedirect('/classes');
        $this->assertDatabaseHas('school_classes', ['id' => $class->id, 'name' => 'Renamed Class']);
    }

    public function test_admins_can_delete_a_class(): void
    {
        $class = SchoolClass::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/classes/{$class->id}");

        $response->assertRedirect('/classes');
        $this->assertDatabaseMissing('school_classes', ['id' => $class->id]);
    }

    public function test_teachers_cannot_manage_classes(): void
    {
        $class = SchoolClass::factory()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/classes')->assertStatus(403);
        $this->actingAs($teacher)->delete("/classes/{$class->id}")->assertStatus(403);
    }
}
