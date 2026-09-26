<?php

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

describe('show', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/settings')->assertStatus(401);
    });

    it('returns empty settings when none have been saved', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('data.company_name', null)
            ->assertJsonPath('data.logo_url', null);
    });

    it('returns the saved settings to any signed-in user', function () {
        CompanySetting::factory()->withLogo()->create(['company_name' => 'Acme Ltd']);

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Acme Ltd')
            ->assertJsonPath('data.logo_url', Storage::disk('public')->url('company/logo.png'));
    });
});

describe('update', function () {
    it('returns 403 for a user without the settings.update permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/settings', ['company_name' => 'Acme Ltd'])
            ->assertStatus(403);
    });

    it('requires a company name', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', [])
            ->assertStatus(422);
    });

    it('rejects a logo that is not an image', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', [
                'company_name' => 'Acme Ltd',
                'logo' => UploadedFile::fake()->create('logo.pdf', 10, 'application/pdf'),
            ])
            ->assertStatus(422);
    });

    it('creates the settings row with an uploaded logo', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', [
                'company_name' => 'Acme Ltd',
                'email' => 'info@acme.test',
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Acme Ltd');

        $setting = CompanySetting::sole();

        expect($setting->email)->toBe('info@acme.test');
        Storage::disk('public')->assertExists($setting->logo);
    });

    it('updates the existing row and replaces the old logo', function () {
        Storage::disk('public')->put('company/logo.png', 'old');
        CompanySetting::factory()->withLogo()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', [
                'company_name' => 'New Name',
                'logo' => UploadedFile::fake()->image('new.png'),
            ])
            ->assertOk();

        $setting = CompanySetting::sole();

        expect($setting->company_name)->toBe('New Name')
            ->and($setting->logo)->not->toBe('company/logo.png');
        Storage::disk('public')->assertMissing('company/logo.png');
        Storage::disk('public')->assertExists($setting->logo);
    });

    it('keeps the current logo when no new one is uploaded', function () {
        CompanySetting::factory()->withLogo()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', ['company_name' => 'Acme Ltd'])
            ->assertOk();

        expect(CompanySetting::sole()->logo)->toBe('company/logo.png');
    });

    it('removes the logo on request', function () {
        Storage::disk('public')->put('company/logo.png', 'old');
        CompanySetting::factory()->withLogo()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/settings', ['company_name' => 'Acme Ltd', 'remove_logo' => true])
            ->assertOk()
            ->assertJsonPath('data.logo_url', null);

        Storage::disk('public')->assertMissing('company/logo.png');
    });
});
