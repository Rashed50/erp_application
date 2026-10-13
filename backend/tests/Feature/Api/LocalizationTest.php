<?php

use App\Models\User;

it('returns english messages by default', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'password'])
        ->assertOk()
        ->assertHeader('Content-Language', 'en')
        ->assertJsonPath('message', 'Logged in successfully.');
});

it('returns bangla messages when the X-Locale header is bn', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->withHeader('X-Locale', 'bn')
        ->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'password'])
        ->assertOk()
        ->assertHeader('Content-Language', 'bn')
        ->assertJsonPath('message', 'সফলভাবে লগইন হয়েছে।');
});

it('translates validation errors and attribute names into bangla', function () {
    $this->withHeader('X-Locale', 'bn')
        ->postJson('/api/login', [])
        ->assertStatus(422)
        ->assertJsonPath('data.email.0', 'ইমেইল আবশ্যক।');
});

it('falls back to Accept-Language when no X-Locale header is sent', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->withHeader('Accept-Language', 'bn-BD,bn;q=0.9,en;q=0.8')
        ->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'wrong-password'])
        ->assertStatus(422)
        ->assertJsonPath('data.email.0', 'প্রদত্ত লগইন তথ্য সঠিক নয়।');
});

it('ignores unsupported locales', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->withHeader('X-Locale', 'fr')
        ->postJson('/api/login', ['email' => 'jane@example.com', 'password' => 'password'])
        ->assertOk()
        ->assertHeader('Content-Language', 'en')
        ->assertJsonPath('message', 'Logged in successfully.');
});
