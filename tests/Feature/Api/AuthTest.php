<?php

namespace Tests\Feature\Api;

use App\Services\AuthService;
use App\DTOs\Auth\AuthDTO;
use App\Models\User;
use Mockery;
use Mockery\MockInterface;

/*
|--------------------------------------------------------------------------
| Authentication API Tests
|--------------------------------------------------------------------------
*/

/**
 * Test successful Login with User creation to satisfy Validation
 */
it('authenticates a user and returns an access token', function () {
    // 1. Arrange: Create the user in the database so 'exists:users,email' rule passes
    User::factory()->create([
        'email' => 'senior@dev.com',
    ]);

    $this->instance(
        AuthService::class,
        Mockery::mock(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->with(Mockery::type(AuthDTO::class))
                ->andReturn([
                    'user'  => (object)['id' => 1, 'email' => 'senior@dev.com'],
                    'token' => 'mocked-sanct_token_123'
                ]);
        })
    );

    // 2. Act: Execute the POST request
    $response = $this->postJson('/api/login', [
        'email'    => 'senior@dev.com',
        'password' => 'password123'
    ]);

    // 3. Assert: Check structure and specific values
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'access_token',
                'token_type'
            ]
        ])
        ->assertJsonPath('data.access_token', 'mocked-sanct_token_123');
});

/**
 * Test Validation failure for empty request
 */
it('returns 422 validation error for invalid login data', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

/**
 * Test successful Registration with Mocking
 */
it('registers a new user and returns a token', function () {
    $this->instance(
        AuthService::class,
        Mockery::mock(AuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('register')
                ->once()
                ->with(Mockery::type(AuthDTO::class))
                ->andReturn([
                    'user'  => (object)['id' => 2, 'email' => 'new@dev.com'],
                    'token' => 'new-user-token-456'
                ]);
        })
    );

    $response = $this->postJson('/api/register', [
        'email'                 => 'new@dev.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.access_token', 'new-user-token-456');
});
