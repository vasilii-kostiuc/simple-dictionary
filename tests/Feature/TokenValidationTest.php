<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_validation_succeeds_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('auth.token.validate'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'valid',

                ]
            ])
            ->assertJson([
                'data' => [
                    'valid' => true,
                ]
            ]);
    }

    public function test_token_validation_fails_without_token(): void
    {
        $response = $this->postJson(route('auth.token.validate'));

        $response->assertStatus(401);
    }

    public function test_token_validation_succeeds_with_user_token_parameter(): void
    {
        // Создаем WSS сервер (или админа) с токеном
        $wssUser = User::factory()->create(['name' => 'WSS Server']);
        $this->actingAs($wssUser);

        // Создаем обычного пользователя и получаем его токен
        $targetUser = User::factory()->create(['name' => 'Target User']);
        $userToken = $targetUser->createToken('test-token')->plainTextToken;

        // WSS сервер проверяет токен пользователя
        $response = $this->postJson(route('auth.token.validate'), [
            'user_token' => $userToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'valid',
                ]
            ])
            ->assertJson([
                'data' => [
                    'valid' => true,
                ]
            ]);
    }

    public function test_token_validation_fails_with_invalid_user_token(): void
    {
        $wssUser = User::factory()->create(['name' => 'WSS Server']);
        $this->actingAs($wssUser);

        $response = $this->postJson(route('auth.token.validate'), [
            'user_token' => 'invalid_token_here',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'data' => [
                    'valid' => false,
                    'message' => 'Invalid token',
                ]
            ]);
    }
}
