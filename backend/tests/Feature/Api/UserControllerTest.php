<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

function adminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

    return $user;
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/users')->assertStatus(401);
    });

    it('returns a paginated list of users', function () {
        $actor = adminUser();
        User::factory()->count(3)->create();

        $response = $this->actingAs($actor, 'sanctum')->getJson('/api/users');

        $response->assertOk()
            ->assertJson(['success' => true, 'code' => 200])
            ->assertJsonCount(4, 'data.users')
            ->assertJsonPath('data.meta.total', 4);
    });
});

describe('show', function () {
    it('returns 401 when no token is provided', function () {
        $user = User::factory()->create();

        $this->getJson("/api/users/{$user->id}")->assertStatus(401);
    });

    it('returns 404 for a user that does not exist', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->getJson('/api/users/999')
            ->assertStatus(404)
            ->assertJson(['success' => false, 'code' => 404]);
    });

    it('returns the requested user', function () {
        $actor = adminUser();
        $user = User::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    });
});

describe('store', function () {
    it('returns 401 when no token is provided', function () {
        $this->postJson('/api/users', [])->assertStatus(401);
    });

    it('requires a name, email, and password', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/users', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['name', 'email', 'password']]);
    });

    it('rejects an email that is already taken', function () {
        $actor = adminUser();
        $existing = User::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'New User',
                'email' => $existing->email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('rejects a password that does not match its confirmation', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'New User',
                'email' => 'new-user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'something-else',
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['password']]);
    });

    it('creates a user and persists it', function () {
        $actor = adminUser();

        $response = $this->actingAs($actor, 'sanctum')->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'User created successfully.'])
            ->assertJsonPath('data.email', 'new-user@example.com');

        $this->assertDatabaseHas('users', ['email' => 'new-user@example.com']);
    });
});

describe('update', function () {
    it('returns 401 when no token is provided', function () {
        $user = User::factory()->create();

        $this->putJson("/api/users/{$user->id}", [])->assertStatus(401);
    });

    it('rejects an email already taken by another user', function () {
        $actor = adminUser();
        $other = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/users/{$target->id}", ['email' => $other->email])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['email']]);
    });

    it('allows a user to keep their own email unchanged', function () {
        $actor = adminUser();
        $target = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/users/{$target->id}", ['name' => 'New Name', 'email' => $target->email])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');
    });

    it('updates the user and persists the change', function () {
        $actor = adminUser();
        $target = User::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->putJson("/api/users/{$target->id}", ['name' => 'Updated Name']);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Updated Name']);
    });

    it('updates the password when provided', function () {
        $actor = adminUser();
        $target = User::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/users/{$target->id}", [
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertOk();

        expect(Hash::check('new-password', $target->fresh()->password))->toBeTrue();
    });
});

describe('destroy', function () {
    it('returns 401 when no token is provided', function () {
        $user = User::factory()->create();

        $this->deleteJson("/api/users/{$user->id}")->assertStatus(401);
    });

    it('rejects deleting your own account', function () {
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/users/{$actor->id}")
            ->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'You cannot delete your own account.']);

        $this->assertDatabaseHas('users', ['id' => $actor->id]);
    });

    it('deletes another user', function () {
        $actor = adminUser();
        $target = User::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/users/{$target->id}")
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'User deleted successfully.']);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    });
});
