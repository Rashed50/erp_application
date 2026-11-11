<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create(['email' => 'me@example.com', 'password' => 'old-password']);
});

describe('profile', function () {
    it('requires a token', function () {
        $this->getJson('/api/profile')->assertStatus(401);
    });

    it('returns the signed-in user', function () {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/profile')
            ->assertOk()
            ->assertJsonPath('data.email', 'me@example.com')
            ->assertJsonPath('data.image_url', null);
    });

    it('updates name, email and photo, replacing the old photo', function () {
        $this->actingAs($this->user, 'sanctum')
            ->post('/api/profile', ['name' => 'New Name', 'email' => 'new@example.com', 'image' => UploadedFile::fake()->image('a.png')])
            ->assertOk();
        $firstImage = $this->user->fresh()->image;

        $response = $this->actingAs($this->user, 'sanctum')
            ->post('/api/profile', ['name' => 'New Name', 'email' => 'new@example.com', 'image' => UploadedFile::fake()->image('b.png')])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $user = $this->user->fresh();
        expect($user->email)->toBe('new@example.com')
            ->and($response->json('data.image_url'))->toBe(Storage::disk('public')->url($user->image));
        Storage::disk('public')->assertExists($user->image);
        Storage::disk('public')->assertMissing($firstImage);
    });

    it('keeps the photo when none is uploaded', function () {
        $this->user->update(['image' => 'users/kept.png']);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile', ['name' => 'Renamed', 'email' => 'me@example.com'])
            ->assertOk();

        expect($this->user->fresh()->image)->toBe('users/kept.png');
    });

    it("rejects another user's email", function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/profile', ['name' => 'Me', 'email' => 'taken@example.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email', 'data');
    });
});

describe('password', function () {
    it('changes the password and signs out other devices only', function () {
        $this->user->createToken('other');
        $current = $this->user->createToken('current')->plainTextToken;

        $this->withToken($current)
            ->putJson('/api/profile/password', [
                'current_password' => 'old-password',
                'new_password' => 'new-password',
                'new_password_confirmation' => 'new-password',
            ])
            ->assertOk();

        expect(Hash::check('new-password', $this->user->fresh()->password))->toBeTrue()
            ->and($this->user->tokens()->pluck('name')->all())->toBe(['current']);
    });

    it('rejects a wrong current password', function () {
        $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/profile/password', [
                'current_password' => 'wrong',
                'new_password' => 'new-password',
                'new_password_confirmation' => 'new-password',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('current_password', 'data');

        expect(Hash::check('old-password', $this->user->fresh()->password))->toBeTrue();
    });

    it('requires a confirmed password of at least 8 characters', function () {
        $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/profile/password', [
                'current_password' => 'old-password',
                'new_password' => 'short',
                'new_password_confirmation' => 'other',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('new_password', 'data');
    });
});
