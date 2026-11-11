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

/**
 * Logs in a fresh user and returns the plain-text API token.
 */
function loginToken(): string
{
    User::factory()->create(['email' => 'jane@example.com']);

    return test()->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'password'])->json('data.token');
}

describe('inactivity timeout', function () {
    it('tells the client the inactivity timeout', function () {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('data.idle_timeout', 30);
    });

    it('keeps an active login alive beyond 30 minutes', function () {
        $token = loginToken();

        foreach (range(1, 3) as $request) {
            $this->travel(20)->minutes();
            $this->app['auth']->forgetGuards();
            $this->withToken($token)->getJson('/api/user')->assertOk();
        }
    });

    it('signs out a login that has been idle for more than 30 minutes', function () {
        $token = loginToken();

        $this->travel(20)->minutes();
        $this->withToken($token)->getJson('/api/user')->assertOk();

        $this->travel(31)->minutes();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
    });

    it('rejects an old token that was never used for more than 30 minutes', function () {
        $token = User::factory()->create()->createToken('api')->plainTextToken;

        $this->travel(31)->minutes();

        $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
    });
});
