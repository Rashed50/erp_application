<?php

use App\Models\ChartOfAccount;
use App\Models\FundTransfer;
use App\Models\User;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
    $this->bank = ChartOfAccount::query()->where('account_number', '1020')->first();
    $this->bankCharges = ChartOfAccount::query()->where('account_number', '5090')->first();
});

/**
 * @return array<string, mixed>
 */
function fundTransferPayload(ChartOfAccount $sender, ChartOfAccount $receiver, array $overrides = []): array
{
    return [
        'credit_account_id' => $sender->id,
        'debit_account_id' => $receiver->id,
        'receipt_no' => 'RCPT-1001',
        'transfer_date' => today()->toDateString(),
        'amount' => 1000,
        'bank_charge' => 20,
        'vat' => 5,
        'remarks' => 'Cash deposit to bank',
        ...$overrides,
    ];
}

describe('index', function () {
    it('returns 401 when no token is provided', function () {
        $this->getJson('/api/accounting/internal-fund-transfer/list')->assertStatus(401);
    });

    it('is forbidden without the view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/accounting/internal-fund-transfer/list')
            ->assertForbidden();
    });

    it('lists transfers and filters them by account', function () {
        FundTransfer::factory()->create(['credit_account_id' => $this->cash->id]);
        FundTransfer::factory()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson("/api/accounting/internal-fund-transfer/list?account_id={$this->cash->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data.transfers')
            ->assertJsonPath('data.transfers.0.credit_account_name', $this->cash->name);
    });
});

describe('store', function () {
    it('validates the required fields', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', [])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['credit_account_id', 'debit_account_id', 'transfer_date', 'amount']]);
    });

    it('credits the sender with the total, debits the receiver and bank charges', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->bank))
            ->assertStatus(201)
            ->assertJsonPath('data.amount', 1000)
            ->assertJsonPath('data.total_amount', 1025);

        expect((float) $this->cash->fresh()->balance)->toBe(-1025.0)
            ->and((float) $this->bank->fresh()->balance)->toBe(1000.0)
            ->and((float) $this->bankCharges->fresh()->balance)->toBe(25.0);
    });

    it('posts VAT to bank charges even without a bank charge', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->bank, ['bank_charge' => 0, 'vat' => 5]))
            ->assertStatus(201);

        expect((float) $this->bankCharges->fresh()->balance)->toBe(5.0)
            ->and((float) $this->cash->fresh()->balance)->toBe(-1005.0);
    });

    it('rejects transferring to the same account', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->cash))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['credit_account_id']]);
    });

    it('rejects accounts that are not open asset transaction accounts', function (string $field, string $accountNumber) {
        $account = ChartOfAccount::query()->where('account_number', $accountNumber)->first();
        $payload = fundTransferPayload($this->cash, $this->bank, [$field => $account->id]);

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', $payload)
            ->assertStatus(422)
            ->assertJsonStructure(['data' => [$field]]);
    })->with([
        'expense sender' => ['credit_account_id', '5010'],
        'group receiver' => ['debit_account_id', '1000'],
    ]);

    it('rejects a future transfer date', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->bank, [
                'transfer_date' => today()->addDay()->toDateString(),
            ]))
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['transfer_date']]);
    });

    it('stores an attachment', function () {
        Storage::fake('public');

        $this->actingAs(adminUser(), 'sanctum')
            ->post('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->bank, [
                'attachment' => UploadedFile::fake()->create('slip.pdf', 100, 'application/pdf'),
            ]), ['Accept' => 'application/json'])
            ->assertStatus(201);

        Storage::disk('public')->assertExists(FundTransfer::first()->attachment);
    });
});

describe('destroy', function () {
    it('reverses every posting and soft deletes the transfer', function () {
        $actor = adminUser();

        $transferId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/accounting/internal-fund-transfer-store', fundTransferPayload($this->cash, $this->bank))
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')->deleteJson("/api/accounting/internal-fund-transfer/{$transferId}")->assertOk();

        expect((float) $this->cash->fresh()->balance)->toBe(0.0)
            ->and((float) $this->bank->fresh()->balance)->toBe(0.0)
            ->and((float) $this->bankCharges->fresh()->balance)->toBe(0.0)
            ->and(FundTransfer::withTrashed()->find($transferId)->deleted_by)->toBe($actor->id);

        $this->assertSoftDeleted('fund_transfers', ['id' => $transferId]);
    });
});

it('approves a transfer', function () {
    $actor = adminUser();
    $transfer = FundTransfer::factory()->create();

    $this->actingAs($actor, 'sanctum')
        ->postJson("/api/accounting/internal-fund-transfer/{$transfer->id}/approve")
        ->assertOk()
        ->assertJsonPath('data.approved_by', $actor->id);
});
