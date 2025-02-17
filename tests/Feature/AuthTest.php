<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_user_logout_success(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
        ]);

        $response = $this->actingAs($user)->getJson(route('dictionaries.index'));

        $response->assertStatus(200);

        $response = $this->actingAs($user)->postJson(route('auth.logout'),[]);

        $response->assertStatus(200)->assertJsonStructure(['message']);

        $this->assertCount(0, $user->tokens);
    }
}
