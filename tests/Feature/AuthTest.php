<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonMissingPath('user.password');

        $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
    }

    public function test_register_rejects_existing_email(): void
    {
        User::factory()->create(['email' => 'maria@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_register_validates_fields(): void
    {
        $this->postJson('/api/register', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => 'senha-segura']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'senha-segura',
        ])->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token']);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $user = User::factory()->create(['password' => 'senha-segura']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'errada',
        ])->assertUnauthorized()
            ->assertJsonPath('message', __('auth.failed'));
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->withToken($user->createToken('api')->plainTextToken)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();

        $this->withToken($user->createToken('api')->plainTextToken)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_protected_routes_require_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
        $this->postJson('/api/logout')->assertUnauthorized();
        $this->withToken('token-invalido')->getJson('/api/me')->assertUnauthorized();
    }
}
