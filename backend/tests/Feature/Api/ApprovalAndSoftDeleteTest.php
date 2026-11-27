<?php

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkOrder;
use Database\Seeders\ChartOfAccountSeeder;

/**
 * A sale of 88.00 (net) for the customer.
 *
 * @return array<string, mixed>
 */
function pendingSalePayload(int $customerId): array
{
    return [
        'customer_id' => $customerId,
        'invoice_number' => 'SINV-'.fake()->unique()->numerify('#####'),
        'description' => 'Consulting services',
        'issue_date' => now()->toDateString(),
        'items' => [
            ['item_name' => 'Consulting', 'qty' => 10, 'unit_price' => 5, 'discount' => 0, 'vat' => 0],
            ['item_name' => 'Support', 'qty' => 20, 'unit_price' => 2, 'discount' => 5, 'vat' => 3],
        ],
    ];
}

dataset('approvable resources', [
    'customers' => ['customers', fn () => Customer::factory()->pending()->create()],
    'suppliers' => ['suppliers', fn () => Supplier::factory()->pending()->create()],
    'purchases' => ['purchases', fn () => Purchase::factory()->pending()->create()],
    'sales' => ['sales', fn () => Sale::factory()->pending()->create()],
    'ledger-accounts' => ['ledger-accounts', fn () => ChartOfAccount::factory()->pending()->create()],
    'work-orders' => ['work-orders', fn () => WorkOrder::factory()->pending()->create()],
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

describe('enforcement', function () {
    beforeEach(function () {
        $this->seed(ChartOfAccountSeeder::class);
        $this->actor = adminUser();
    });

    it('refuses a sale for a customer that is not approved', function () {
        $customer = Customer::factory()->pending()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', pendingSalePayload($customer->id))
            ->assertUnprocessable()
            ->assertJsonStructure(['data' => ['customer_id']]);
    });

    it('refuses a work order for a customer that is not approved', function () {
        $customer = Customer::factory()->pending()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/work-orders', ['customer_id' => $customer->id, 'work_title' => 'Fit-out', 'work_order_no' => 'WO-1', 'issue_date' => '2026-01-01', 'total_amount' => 1000])
            ->assertUnprocessable()
            ->assertJsonStructure(['data' => ['customer_id']]);
    });

    it('refuses an unapproved ledger account as a payment account', function () {
        $account = ChartOfAccount::factory()->pending()->create(['account_type_id' => AccountType::ASSET, 'is_transaction' => true]);
        $customer = Customer::factory()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", ['transaction_type' => CustomerTransaction::TYPE_PAYMENT_RECEIVED, 'credit' => 10, 'payment_account_id' => $account->id, 'transaction_date' => '2026-01-01'])
            ->assertUnprocessable()
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    });

    it('refuses a ledger transaction for a customer that is not approved', function () {
        $customer = Customer::factory()->pending()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/customers/{$customer->id}/transactions", ['transaction_type' => CustomerTransaction::TYPE_ADJUSTMENT, 'debit' => 10, 'transaction_date' => '2026-01-01'])
            ->assertUnprocessable();
    });

    it('keeps a new sale off the books until it is approved, then posts it', function () {
        $customer = Customer::factory()->create();

        $sale = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', pendingSalePayload($customer->id))
            ->assertCreated()
            ->assertJsonPath('data.approved_at', null)
            ->json('data');

        expect(ledgerBalance('1030'))->toBe(0.0)
            ->and(CustomerTransaction::query()->count())->toBe(0);

        $this->actingAs($this->actor, 'sanctum')->postJson("/api/sales/{$sale['id']}/approve")->assertOk();

        expect(ledgerBalance('1030'))->toBe(88.0)
            ->and(CustomerTransaction::query()->where('transaction_type', 'Invoice')->sole()->debit)->toEqual(88);
    });

    it('refuses a payment against a sale that is not approved', function () {
        $sale = Sale::factory()->pending()->withTotal(100)->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/sales/{$sale->id}/payments", ['payment_account_id' => cashInHandAccountId(), 'amount' => 10, 'payment_date' => now()->toDateString()])
            ->assertUnprocessable();
    });

    it('sends an edited sale back for approval and takes it off the books', function () {
        $customer = Customer::factory()->create();
        $saleId = $this->actingAs($this->actor, 'sanctum')->postJson('/api/sales', pendingSalePayload($customer->id))->json('data.id');
        $this->actingAs($this->actor, 'sanctum')->postJson("/api/sales/{$saleId}/approve")->assertOk();

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/sales/{$saleId}", ['description' => 'Changed'])
            ->assertOk()
            ->assertJsonPath('data.approved_at', null);

        expect(ledgerBalance('1030'))->toBe(0.0)
            ->and(Sale::find($saleId)->ledgerTransaction->status)->toBeFalse();

        $this->actingAs($this->actor, 'sanctum')->postJson("/api/sales/{$saleId}/approve")->assertOk();

        expect(ledgerBalance('1030'))->toBe(88.0)
            ->and(Sale::find($saleId)->ledgerTransaction->status)->toBeTrue();
    });

    it('sends an edited customer back for approval and reposts its opening balance on approval', function () {
        $customerId = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/customers', ['name' => 'Acme', 'opening_balance' => 500])
            ->assertCreated()
            ->json('data.id');

        expect(ledgerBalance('1030'))->toBe(0.0);

        $this->actingAs($this->actor, 'sanctum')->postJson("/api/customers/{$customerId}/approve")->assertOk();
        expect(ledgerBalance('1030'))->toBe(500.0);

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/customers/{$customerId}", ['name' => 'Acme Ltd'])
            ->assertOk()
            ->assertJsonPath('data.approved_at', null);
        expect(ledgerBalance('1030'))->toBe(0.0);

        $this->actingAs($this->actor, 'sanctum')->postJson("/api/customers/{$customerId}/approve")->assertOk();
        expect(ledgerBalance('1030'))->toBe(500.0);
    });

    it('lists only approved customers when asked to', function () {
        Customer::factory()->create();
        Customer::factory()->pending()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/customers?approved=1')
            ->assertOk()
            ->assertJsonCount(1, 'data.customers');
    });
});
