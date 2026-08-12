<?php

namespace Tests\Feature;

use App\Enums\DayOfWeek;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimetableManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_timetable_list(): void
    {
        Timetable::factory(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/timetables');

        $response->assertStatus(200);
    }

    public function test_admins_can_create_a_timetable_entry(): void
    {
        $class = SchoolClass::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->post('/timetables', [
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'day_of_week' => DayOfWeek::Monday->value,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertRedirect('/timetables');
        $this->assertDatabaseHas('timetables', ['school_class_id' => $class->id, 'day_of_week' => DayOfWeek::Monday->value]);
    }

    public function test_creating_a_duplicate_timetable_slot_fails_validation(): void
    {
        $existing = Timetable::factory()->create([
            'day_of_week' => DayOfWeek::Tuesday->value,
            'start_time' => '09:00',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())->post('/timetables', [
            'school_class_id' => $existing->school_class_id,
            'subject_id' => Subject::factory()->create()->id,
            'teacher_id' => Teacher::factory()->create()->id,
            'day_of_week' => DayOfWeek::Tuesday->value,
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('start_time');
    }

    public function test_admins_can_delete_a_timetable_entry(): void
    {
        $timetable = Timetable::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/timetables/{$timetable->id}");

        $response->assertRedirect('/timetables');
        $this->assertDatabaseMissing('timetables', ['id' => $timetable->id]);
    }

    public function test_teachers_cannot_manage_timetables(): void
    {
        $timetable = Timetable::factory()->create();
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/timetables')->assertStatus(403);
        $this->actingAs($teacher)->delete("/timetables/{$timetable->id}")->assertStatus(403);
    }
}
