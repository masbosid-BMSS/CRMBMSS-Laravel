<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('CRM BMSS');
    }

    public function test_users_can_authenticate_using_valid_credentials(): void
    {
        $user = User::create([
            'id' => 'u_test',
            'name' => 'Test CS',
            'username' => 'test.cs',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'username' => 'test.cs',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::create([
            'id' => 'u_test',
            'name' => 'Test CS',
            'username' => 'test.cs',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'username' => 'test.cs',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
