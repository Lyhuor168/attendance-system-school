<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianPortalTest extends TestCase
{
    use RefreshDatabase;

    private function makeGuardianWithChild(): array
    {
        $guardianUser = User::factory()->create(['role' => Role::Guardian]);
        $guardian = Guardian::factory()->create(['user_id' => $guardianUser->id]);
        $class = SchoolClass::factory()->create();
        $child = Student::factory()->create(['school_class_id' => $class->id, 'guardian_id' => $guardian->id]);

        return [$guardianUser, $guardian, $child];
    }

    public function test_guardian_can_log_in_and_see_their_children(): void
    {
        [$guardianUser, , $child] = $this->makeGuardianWithChild();

        $response = $this->actingAs($guardianUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Your Children');
        $response->assertSeeText($child->name);
    }

    public function test_guardian_dashboard_does_not_show_other_guardians_children(): void
    {
        [$guardianUser] = $this->makeGuardianWithChild();
        [, , $otherChild] = $this->makeGuardianWithChild();

        $response = $this->actingAs($guardianUser)->get('/dashboard');

        $response->assertDontSeeText($otherChild->name);
    }

    public function test_guardian_without_linked_profile_sees_empty_state(): void
    {
        $guardianUser = User::factory()->create(['role' => Role::Guardian]);

        $response = $this->actingAs($guardianUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('No guardian profile is linked to your account yet.');
    }

    public function test_guardian_is_blocked_from_admin_only_routes(): void
    {
        [$guardianUser] = $this->makeGuardianWithChild();

        $this->actingAs($guardianUser)->get('/students')->assertStatus(403);
        $this->actingAs($guardianUser)->get('/teachers')->assertStatus(403);
        $this->actingAs($guardianUser)->get('/payments')->assertStatus(403);
    }

    public function test_guardian_is_blocked_from_attendance_and_leave_requests(): void
    {
        [$guardianUser] = $this->makeGuardianWithChild();

        // Attendance index has no role gate at the route level (it degrades
        // to an empty list for non-teachers), but the class-scoped actions
        // must still reject a Guardian outright.
        $class = SchoolClass::factory()->create();
        $this->actingAs($guardianUser)->get("/attendance/{$class->id}/record")->assertStatus(403);
        $this->actingAs($guardianUser)->get('/leave-requests/create')->assertStatus(403);
    }
}
