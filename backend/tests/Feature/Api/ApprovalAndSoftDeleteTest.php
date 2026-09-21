<?php

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;

dataset('approvable resources', [
    'customers' => ['customers', fn () => Customer::factory()->create()],
    'suppliers' => ['suppliers', fn () => Supplier::factory()->create()],
    'purchases' => ['purchases', fn () => Purchase::factory()->create()],
    'sales' => ['sales', fn () => Sale::factory()->create()],
    'ledger-accounts' => ['ledger-accounts', fn () => ChartOfAccount::factory()->create()],
]);

it('requires authentication to approve', function () {
    $customer = Customer::factory()->create();

    $this->postJson("/api/customers/{$customer->id}/approve")->assertStatus(401);
});

it('is forbidden without the approve permission', function () {
    $customer = Customer::factory()->create();

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson("/api/customers/{$customer->id}/approve")
        ->assertForbidden();
});

it('approves a record and records the approver', function (string $uri, Closure $make) {
    $actor = adminUser();
    $record = $make();

    $this->actingAs($actor, 'sanctum')
        ->postJson("/api/{$uri}/{$record->id}/approve")
        ->assertOk()
        ->assertJsonPath('data.approved_by', $actor->id);

    expect($record->fresh()->approver->is($actor))->toBeTrue();
})->with('approvable resources');

it('rejects approving an already approved record', function () {
    $actor = adminUser();
    $customer = Customer::factory()->create(['approved_by' => $actor->id, 'approved_at' => now()]);

    $this->actingAs($actor, 'sanctum')
        ->postJson("/api/customers/{$customer->id}/approve")
        ->assertStatus(422);
});

it('soft deletes a customer and records who deleted it', function () {
    $actor = adminUser();
    $customer = Customer::factory()->create();

    $this->actingAs($actor, 'sanctum')->deleteJson("/api/customers/{$customer->id}")->assertOk();

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    expect(Customer::withTrashed()->find($customer->id)->deleted_by)->toBe($actor->id);
});
