<?php

use App\Models\Department;
use App\Models\Designation;
use App\Models\District;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Upazila;
use App\Models\User;

dataset('setup tables', [
    'departments' => ['departments', Department::class],
    'designations' => ['designations', Designation::class],
]);

describe('departments and designations', function () {
    it('returns 403 for a user without the view permission', function (string $resource) {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson("/api/hr/{$resource}")
            ->assertStatus(403);
    })->with('setup tables');

    it('lists, searches and filters by status', function (string $resource, string $model) {
        $model::factory()->create(['name' => 'Accounts']);
        $model::factory()->inactive()->create(['name' => 'Logistics']);

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/hr/{$resource}?search=Acc")
            ->assertOk()
            ->assertJsonCount(1, "data.{$resource}")
            ->assertJsonPath("data.{$resource}.0.name", 'Accounts');

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/hr/{$resource}?status=0")
            ->assertOk()
            ->assertJsonCount(1, "data.{$resource}")
            ->assertJsonPath("data.{$resource}.0.name", 'Logistics');
    })->with('setup tables');

    it('creates a record and rejects a duplicate name', function (string $resource) {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/hr/{$resource}", ['name' => 'Accounts', 'description' => 'Books and ledgers'])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Accounts')
            ->assertJsonPath('data.status', true);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson("/api/hr/{$resource}", ['name' => 'Accounts'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'], 'data');
    })->with('setup tables');

    it('updates a record keeping its own name valid', function (string $resource, string $model) {
        $record = $model::factory()->create(['name' => 'Accounts']);

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/hr/{$resource}/{$record->id}", ['name' => 'Accounts', 'description' => 'Updated'])
            ->assertOk()
            ->assertJsonPath('data.description', 'Updated');
    })->with('setup tables');

    it('deactivates a record', function (string $resource, string $model) {
        $record = $model::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->patchJson("/api/hr/{$resource}/{$record->id}/status", ['status' => false])
            ->assertOk()
            ->assertJsonPath('data.status', false);
    })->with('setup tables');

    it('offers every department and designation on the employee options', function () {
        Department::factory()->create(['name' => 'Accounts']);
        Designation::factory()->inactive()->create(['name' => 'Officer']);

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/hr/employees/options')
            ->assertOk()
            ->assertJsonPath('data.departments.0.name', 'Accounts')
            ->assertJsonPath('data.designations.0.name', 'Officer')
            ->assertJsonPath('data.designations.0.status', false);
    });

    it('counts the employees in each department', function () {
        Employee::factory()->count(2)->inDepartment('Accounts')->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/hr/departments')
            ->assertOk()
            ->assertJsonPath('data.departments.0.employees_count', 2);
    });
});

describe('locations', function () {
    it('lists divisions, then the districts of a division, then the thanas of a district', function () {
        $dhaka = Division::create(['name' => 'Dhaka']);
        $sylhet = Division::create(['name' => 'Sylhet']);
        $gazipur = District::create(['division_id' => $dhaka->id, 'name' => 'Gazipur']);
        District::create(['division_id' => $sylhet->id, 'name' => 'Moulvibazar']);
        Upazila::create(['district_id' => $gazipur->id, 'name' => 'Tongi']);

        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/locations/divisions')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->actingAs($user, 'sanctum')->getJson("/api/locations/districts?division_id={$dhaka->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Gazipur');

        $this->actingAs($user, 'sanctum')->getJson("/api/locations/upazilas?district_id={$gazipur->id}")
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Tongi');
    });

    it('adds a division, a district under it and a thana under that', function () {
        $divisionId = $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/locations/divisions', ['name' => 'Dhaka', 'bn_name' => 'ঢাকা'])
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Dhaka')
            ->json('data.id');

        $districtId = $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/locations/districts', ['division_id' => $divisionId, 'name' => 'Gazipur'])
            ->assertStatus(201)
            ->json('data.id');

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/locations/upazilas', ['district_id' => $districtId, 'name' => 'Tongi'])
            ->assertStatus(201)
            ->assertJsonPath('data.district_id', $districtId);
    });

    it('rejects a duplicate district in the same division but allows it in another', function () {
        $dhaka = Division::create(['name' => 'Dhaka']);
        $sylhet = Division::create(['name' => 'Sylhet']);
        District::create(['division_id' => $dhaka->id, 'name' => 'Gazipur']);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/locations/districts', ['division_id' => $dhaka->id, 'name' => 'Gazipur'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'], 'data');

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/locations/districts', ['division_id' => $sylhet->id, 'name' => 'Gazipur'])
            ->assertStatus(201);
    });

    it('forbids adding a location without the locations.create permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson('/api/locations/divisions', ['name' => 'Dhaka'])
            ->assertStatus(403);
    });

    it('requires the parent id', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/locations/districts')
            ->assertStatus(422);
    });
});
