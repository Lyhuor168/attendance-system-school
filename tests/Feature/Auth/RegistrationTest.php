<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_registration_screen(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/login');
    }

    public function test_admins_can_view_the_registration_screen(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/register');

        $response->assertStatus(200);
    }

    public function test_teachers_cannot_view_the_registration_screen(): void
    {
        $response = $this->actingAs(User::factory()->teacher()->create())->get('/register');

        $response->assertStatus(403);
    }

    public function test_admins_can_create_a_new_admin_without_switching_session(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/register', [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(User::where('email', 'new-admin@example.com')->first()->isAdmin());
    }
}
