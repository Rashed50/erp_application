<?php

use App\Models\ChartOfAccount;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
    $this->payable = ChartOfAccount::query()->where('account_number', '2010')->first();
    $this->bankCharges = ChartOfAccount::query()->where('account_number', '5090')->first();
});

/**
 * @return array<string, mixed>
 */
function billPaymentPayload(Supplier $supplier, ChartOfAccount $paymentAccount, array $overrides = []): array
{
    return [
        'supplier_id' => $supplier->id,
        'payment_account_id' => $paymentAccount->id,
        'invoice_no' => 'INV-1001',
        'payment_date' => today()->toDateString(),
        'bill_amount' => 1000,
        'bank_charge' => 50,
        'remarks' => 'March bill',
        ...$overrides,
    ];
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/accounting/purchase/payment/list')->assertStatus(401);
    });

    it('is forbidden without the view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/accounting/purchase/payment/list')
            ->assertForbidden();
    });

    it('lists payments and filters them by supplier', function () {
        $supplier = Supplier::factory()->create();
        SupplierPayment::factory()->for($supplier)->create();
        SupplierPayment::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/accounting/purchase/payment/list?supplier_id={$supplier->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data.payments')
            ->assertJsonPath('data.payments.0.supplier_name', $supplier->name);
    });
});

describe('store', function () {
    it('validates the required fields', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['supplier_id', 'payment_account_id', 'payment_date', 'bill_amount']]);
    });

    it('records the payment and posts the double entry and supplier ledger', function () {
        $supplier = Supplier::factory()->create(['opening_balance' => 3000, 'current_balance' => 3000]);

        $response = $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload($supplier, $this->cash));

        $response->assertStatus(201)
            ->assertJsonPath('data.bill_amount', 1000)
            ->assertJsonPath('data.bank_charge', 50)
            ->assertJsonPath('data.total_amount', 1050);

        // Cash (asset) is credited with the total, Accounts Payable (liability)
        // is debited with the bill, Bank Charges (expense) with the charge.
        expect((float) $this->cash->fresh()->balance)->toBe(-1050.0)
            ->and((float) $this->payable->fresh()->balance)->toBe(-1000.0)
            ->and((float) $this->bankCharges->fresh()->balance)->toBe(50.0)
            ->and((float) $supplier->fresh()->current_balance)->toBe(2000.0);

        $this->assertDatabaseHas('supplier_transactions', [
            'supplier_id' => $supplier->id,
            'transaction_type' => 'Bill Payment',
            'invoice_no' => 'INV-1001',
            'debit' => 1000,
        ]);
    });

    it('settles a linked purchase and takes its invoice number', function () {
        $purchase = Purchase::factory()->create(['net_total' => 1500, 'paid_amount' => 0]);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload($purchase->supplier, $this->cash, [
                'purchase_id' => $purchase->id,
                'invoice_no' => null,
            ]))
            ->assertStatus(201)
            ->assertJsonPath('data.invoice_no', $purchase->invoice_number);

        expect((float) $purchase->fresh()->paid_amount)->toBe(1000.0);
    });

    it('rejects a bill amount above the purchase due amount', function () {
        $purchase = Purchase::factory()->create(['net_total' => 500, 'paid_amount' => 0]);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload($purchase->supplier, $this->cash, ['purchase_id' => $purchase->id]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['bill_amount']]);
    });

    it('rejects a purchase of another supplier', function () {
        $purchase = Purchase::factory()->create(['net_total' => 5000]);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload(Supplier::factory()->create(), $this->cash, ['purchase_id' => $purchase->id]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['purchase_id']]);
    });

    it('rejects a credit account that is not an open asset transaction account', function (string $accountNumber) {
        $account = ChartOfAccount::query()->where('account_number', $accountNumber)->first();

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload(Supplier::factory()->create(), $account))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_account_id']]);
    })->with(['expense account' => '5010', 'group account' => '1000']);

    it('rejects a future payment date', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload(Supplier::factory()->create(), $this->cash, [
                'payment_date' => today()->addDay()->toDateString(),
            ]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['payment_date']]);
    });

    it('stores an attachment', function () {
        Storage::fake('public');

        $response = $this->actingAs(adminUser(), 'sanctum')
            ->post('/api/accounting/purchase/payment/store', billPaymentPayload(Supplier::factory()->create(), $this->cash, [
                'attachment' => UploadedFile::fake()->create('bill.pdf', 100, 'application/pdf'),
            ]), ['Accept' => 'application/json']);

        $response->assertStatus(201);
        Storage::disk('public')->assertExists(SupplierPayment::first()->attachment);
    });
});

describe('destroy', function () {
    it('reverses every posting and soft deletes the payment', function () {
        $actor = adminUser();
        $purchase = Purchase::factory()->create(['net_total' => 1500, 'paid_amount' => 0]);
        $supplier = $purchase->supplier;
        $balanceBefore = (float) $supplier->current_balance;

        $paymentId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/accounting/purchase/payment/store', billPaymentPayload($supplier, $this->cash, ['purchase_id' => $purchase->id]))
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')->deleteJson("/api/accounting/purchase/payment/{$paymentId}")->assertOk();

        expect((float) $this->cash->fresh()->balance)->toBe(0.0)
            ->and((float) $this->payable->fresh()->balance)->toBe(0.0)
            ->and((float) $this->bankCharges->fresh()->balance)->toBe(0.0)
            ->and((float) $purchase->fresh()->paid_amount)->toBe(0.0)
            ->and((float) $supplier->fresh()->current_balance)->toBe($balanceBefore)
            ->and(SupplierPayment::withTrashed()->find($paymentId)->deleted_by)->toBe($actor->id);

        $this->assertSoftDeleted('supplier_payments', ['id' => $paymentId]);
    });
});

it('approves a payment', function () {
    $actor = adminUser();
    $payment = SupplierPayment::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->postJson("/api/accounting/purchase/payment/{$payment->id}/approve")
        ->assertOk()
        ->assertJsonPath('data.approved_by', $actor->id);
});
