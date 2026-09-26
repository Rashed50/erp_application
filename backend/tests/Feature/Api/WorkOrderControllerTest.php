<?php

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\User;
use App\Models\WorkOrder;
use Database\Seeders\ChartOfAccountSeeder;

function workOrderPayload(int $customerId, array $overrides = []): array
{
    return array_merge([
        'customer_id' => $customerId,
        'work_title' => 'Office renovation',
        'work_order_no' => 'WO-'.fake()->unique()->numerify('#####'),
        'issue_date' => '2026-09-01',
        'total_amount' => 25000,
        'retention_percent' => 10,
        'deliver_date' => '2026-10-15',
        'status' => 'Pending',
    ], $overrides);
}

function workOrderSalePayload(WorkOrder $workOrder, array $overrides = []): array
{
    return array_merge([
        'customer_id' => $workOrder->customer_id,
        'work_order_id' => $workOrder->id,
        'invoice_number' => 'SINV-'.fake()->unique()->numerify('#####'),
        'issue_date' => now()->toDateString(),
        'items' => [['item_name' => 'Phase 1', 'qty' => 1, 'unit_price' => 600]],
    ], $overrides);
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/work-orders')->assertStatus(401);
    });

    it('is forbidden without the view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/work-orders')
            ->assertForbidden();
    });

    it('returns a paginated list of work orders', function () {
        WorkOrder::factory()->count(3)->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/work-orders')
            ->assertOk()
            ->assertJsonCount(3, 'data.work_orders')
            ->assertJsonPath('data.meta.total', 3);
    });

    it('filters by customer, status, and search term', function () {
        $customer = Customer::factory()->create();
        WorkOrder::factory()->for($customer)->create(['work_order_no' => 'WO-ALPHA', 'status' => 'Completed']);
        WorkOrder::factory()->for($customer)->create(['status' => 'Pending']);
        WorkOrder::factory()->create(['status' => 'Completed']);
        $actor = adminUser();

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/work-orders?customer_id={$customer->id}")
            ->assertJsonCount(2, 'data.work_orders');

        $this->actingAs($actor, 'sanctum')
            ->getJson('/api/work-orders?status=Completed')
            ->assertJsonCount(2, 'data.work_orders');

        $this->actingAs($actor, 'sanctum')
            ->getJson('/api/work-orders?search=ALPHA')
            ->assertJsonCount(1, 'data.work_orders')
            ->assertJsonPath('data.work_orders.0.work_order_no', 'WO-ALPHA');
    });
});

describe('store', function () {
    it('requires customer_id, work_title, work_order_no, issue_date, and total_amount', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/work-orders', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['customer_id', 'work_title', 'work_order_no', 'issue_date', 'total_amount']]);
    });

    it('creates a work order and records the creator', function () {
        $actor = adminUser();
        $customer = Customer::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/work-orders', workOrderPayload($customer->id))
            ->assertStatus(201)
            ->assertJsonPath('data.customer_name', $customer->name)
            ->assertJsonPath('data.total_amount', 25000)
            ->assertJsonPath('data.retention_amount', 2500)
            ->assertJsonPath('data.status', 'Pending')
            ->assertJsonPath('data.created_by', $actor->id)
            ->assertJsonPath('data.approved_by', null);
    });

    it('defaults status to Pending and retention to zero', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/work-orders', workOrderPayload($customer->id, ['status' => null, 'retention_percent' => null]))
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'Pending')
            ->assertJsonPath('data.retention_percent', 0);
    });

    it('rejects a duplicate work_order_no', function () {
        $existing = WorkOrder::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/work-orders', workOrderPayload($existing->customer_id, ['work_order_no' => $existing->work_order_no]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['work_order_no']]);
    });

    it('rejects an invalid status, retention over 100, and a deliver date before issue', function () {
        $customer = Customer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/work-orders', workOrderPayload($customer->id, [
                'status' => 'Unknown',
                'retention_percent' => 120,
                'deliver_date' => '2026-08-01',
            ]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['status', 'retention_percent', 'deliver_date']]);
    });
});

describe('show', function () {
    it('returns a single work order', function () {
        $workOrder = WorkOrder::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/work-orders/{$workOrder->id}")
            ->assertOk()
            ->assertJsonPath('data.work_order_no', $workOrder->work_order_no);
    });
});

describe('update', function () {
    it('updates a work order and records the updater', function () {
        $actor = adminUser();
        $workOrder = WorkOrder::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->putJson("/api/work-orders/{$workOrder->id}", ['status' => 'In Progress', 'work_title' => 'Updated'])
            ->assertOk()
            ->assertJsonPath('data.status', 'In Progress')
            ->assertJsonPath('data.work_title', 'Updated')
            ->assertJsonPath('data.updated_by', $actor->id);
    });

    it('allows keeping its own work_order_no', function () {
        $workOrder = WorkOrder::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->putJson("/api/work-orders/{$workOrder->id}", ['work_order_no' => $workOrder->work_order_no])
            ->assertOk();
    });
});

describe('payment tracking', function () {
    beforeEach(function () {
        (new ChartOfAccountSeeder)->run();
        $this->cashAccountId = ChartOfAccount::query()->where('account_number', '1010')->value('id');
        $this->actor = adminUser();
        $this->workOrder = WorkOrder::factory()->create(['total_amount' => 1000]);
    });

    it('tags the sale invoice and its payments with the work order', function () {
        $saleId = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder))
            ->assertStatus(201)
            ->assertJsonPath('data.work_order_id', $this->workOrder->id)
            ->assertJsonPath('data.work_order_no', $this->workOrder->work_order_no)
            ->json('data.id');

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/sales/{$saleId}/payments", ['payment_account_id' => $this->cashAccountId, 'amount' => 250, 'payment_date' => now()->toDateString()])
            ->assertStatus(201);

        $this->assertDatabaseHas('customer_transactions', ['work_order_id' => $this->workOrder->id, 'transaction_type' => 'Invoice', 'debit' => 600]);
        $this->assertDatabaseHas('customer_transactions', ['work_order_id' => $this->workOrder->id, 'transaction_type' => 'Payment Received', 'credit' => 250]);
    });

    it('lists a customer\'s work orders with paid and outstanding amounts', function () {
        $saleId = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder))
            ->json('data.id');

        foreach ([100, 150] as $amount) {
            $this->actingAs($this->actor, 'sanctum')
                ->postJson("/api/sales/{$saleId}/payments", ['payment_account_id' => $this->cashAccountId, 'amount' => $amount, 'payment_date' => now()->toDateString()])
                ->assertStatus(201);
        }
        WorkOrder::factory()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/customers/{$this->workOrder->customer_id}/work-orders")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.paid_amount', 250)
            ->assertJsonPath('data.0.outstanding_amount', 750)
            ->assertJsonPath('data.0.payments_count', 2);
    });

    it('excludes reversed payments from the paid amount', function () {
        $saleId = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder))
            ->json('data.id');
        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/sales/{$saleId}/payments", ['payment_account_id' => $this->cashAccountId, 'amount' => 200, 'payment_date' => now()->toDateString()]);

        $paymentId = $this->workOrder->transactions()->where('transaction_type', 'Payment Received')->value('id');
        $this->actingAs($this->actor, 'sanctum')
            ->deleteJson("/api/customers/{$this->workOrder->customer_id}/transactions/{$paymentId}")
            ->assertOk();

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/work-orders/{$this->workOrder->id}")
            ->assertJsonPath('data.paid_amount', 0)
            ->assertJsonPath('data.outstanding_amount', 1000)
            ->assertJsonPath('data.payments_count', 0);
    });

    it('counts a Payment Received entered from the customer ledger', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/customers/{$this->workOrder->customer_id}/transactions", [
                'transaction_type' => 'Payment Received',
                'work_order_id' => $this->workOrder->id,
                'credit' => 300,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.work_order_id', $this->workOrder->id);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/work-orders/{$this->workOrder->id}")
            ->assertJsonPath('data.paid_amount', 300)
            ->assertJsonPath('data.outstanding_amount', 700);
    });

    it('rejects a work order that belongs to another customer', function () {
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder, ['customer_id' => $otherCustomer->id]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['work_order_id']]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/customers/{$otherCustomer->id}/transactions", [
                'transaction_type' => 'Payment Received',
                'work_order_id' => $this->workOrder->id,
                'credit' => 10,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['work_order_id']]);
    });

    it('allows changing the sale work order only until a payment is received', function () {
        $otherWorkOrder = WorkOrder::factory()->create(['customer_id' => $this->workOrder->customer_id]);
        $saleId = $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder))
            ->json('data.id');

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/sales/{$saleId}", ['work_order_id' => $otherWorkOrder->id])
            ->assertOk();
        $this->assertDatabaseHas('customer_transactions', ['work_order_id' => $otherWorkOrder->id, 'transaction_type' => 'Invoice']);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/sales/{$saleId}/payments", ['payment_account_id' => $this->cashAccountId, 'amount' => 50, 'payment_date' => now()->toDateString()]);

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/sales/{$saleId}", ['work_order_id' => $this->workOrder->id])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['work_order_id']]);
    });

    it('blocks deleting a work order that has sales', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/sales', workOrderSalePayload($this->workOrder))
            ->assertStatus(201);

        $this->actingAs($this->actor, 'sanctum')
            ->deleteJson("/api/work-orders/{$this->workOrder->id}")
            ->assertStatus(422);
    });
});

describe('customer deletion', function () {
    it('blocks deleting a customer that has work orders', function () {
        $workOrder = WorkOrder::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->deleteJson("/api/customers/{$workOrder->customer_id}")
            ->assertStatus(422)
            ->assertJsonPath('message', 'This customer has work orders and cannot be deleted.');

        $this->assertNotSoftDeleted('customers', ['id' => $workOrder->customer_id]);
    });

    it('allows deleting the customer once its work orders are deleted', function () {
        $workOrder = WorkOrder::factory()->create();
        $workOrder->delete();

        $this->actingAs(adminUser(), 'sanctum')
            ->deleteJson("/api/customers/{$workOrder->customer_id}")
            ->assertOk();
    });
});

describe('destroy', function () {
    it('soft deletes a work order and records who deleted it', function () {
        $actor = adminUser();
        $workOrder = WorkOrder::factory()->create();

        $this->actingAs($actor, 'sanctum')
            ->deleteJson("/api/work-orders/{$workOrder->id}")
            ->assertOk();

        $this->assertSoftDeleted('work_orders', ['id' => $workOrder->id]);
        expect(WorkOrder::withTrashed()->find($workOrder->id)->deleted_by)->toBe($actor->id);
    });
});
