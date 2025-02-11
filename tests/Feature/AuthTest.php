<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_succeeds(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'name' => 'Valid name',
            'email' => 'valid@email.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_login_succeeds(): void
    {
        $user = User::factory()->create();
        $user->save();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
        ]);

        $response->assertStatus(200);
    }
}
