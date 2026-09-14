<?php

use App\Models\User;

it('logs in a user with valid credentials and returns a token', function () {
    $user = User::factory()->create(['email' => 'jane@example.com']);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Logged in successfully.',
            'code' => 200,
        ])
        ->assertJsonPath('data.user.email', 'jane@example.com')
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token']]);

    $this->assertDatabaseCount('personal_access_tokens', 1);
    $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
});

it('rejects login with an incorrect password', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'code' => 422,
        ])
        ->assertJsonPath('data.email.0', 'The provided credentials are incorrect.');

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('rejects login for an email that does not exist', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'missing@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('data.email.0', 'The provided credentials are incorrect.');
});

it('requires an email and a password', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJson(['success' => false, 'code' => 422])
        ->assertJsonStructure(['data' => ['email', 'password']]);
});

it('rejects an unauthenticated request to the logout endpoint', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
            'code' => 401,
        ]);
});

it('logs out an authenticated user and revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
