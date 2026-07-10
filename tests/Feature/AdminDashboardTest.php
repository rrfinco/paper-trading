<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest users should be redirected to login when trying to access the admin dashboard.
     */
    public function test_guests_are_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Regular users with 'user' role should be forbidden from accessing the admin dashboard (403).
     */
    public function test_regular_users_are_forbidden_from_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Admin users with 'admin' role can successfully view the admin dashboard.
     */
    public function test_admins_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Trader Directory');
    }

    /**
     * Admins can load details of a specific user.
     */
    public function test_admins_can_load_user_details_json(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $trader = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);

        $response = $this->actingAs($admin)->get("/admin/users/{$trader->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('user.name', 'John Doe');
        $response->assertJsonPath('user.email', 'john@example.com');
    }

    /**
     * Logging in as an admin redirects directly to the admin dashboard.
     */
    public function test_login_as_admin_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-redirect@example.com',
            'role' => 'admin',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'admin-redirect@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
    }
}
