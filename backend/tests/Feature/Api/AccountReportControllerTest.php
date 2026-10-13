<?php

use App\Models\AccountTransactionDetail;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\User;
use App\Models\WorkOrder;
use Database\Seeders\ChartOfAccountSeeder;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(ChartOfAccountSeeder::class);

    $this->cash = ChartOfAccount::query()->where('account_number', '1010')->first();
    $this->bank = ChartOfAccount::query()->where('account_number', '1020')->first();
    $this->rent = ChartOfAccount::query()->where('account_number', '5030')->first();
    $this->revenue = ChartOfAccount::query()->where('account_number', '4010')->first();
    $this->actor = adminUser();
});

/**
 * Posts an income or expense entry through the API.
 */
function postIncomeExpense(User $actor, string $type, ChartOfAccount $account, ChartOfAccount $paymentAccount, float $amount, string $date): void
{
    test()->actingAs($actor, 'sanctum')->postJson('/api/income-expenses', [
        'type' => $type,
        'income_expense_account_id' => $account->id,
        'payment_account_id' => $paymentAccount->id,
        'amount' => $amount,
        'transaction_date' => $date,
    ])->assertStatus(201);
}

describe('access', function () {
    it('requires the account-reports.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/accounting/reports/balance-sheet')
            ->assertStatus(403);
    });
});

describe('general ledger', function () {
    it('brings the balance forward and keeps a running Dr/Cr balance', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 300, '2026-02-05');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 900, '2026-02-20');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/general-ledger?account_id={$this->cash->id}&from_date=2026-02-01&to_date=2026-02-28")
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 1000)
            ->assertJsonPath('data.opening_balance_type', 'Dr')
            ->assertJsonCount(2, 'data.transactions')
            ->assertJsonPath('data.transactions.0.date', '2026-02-05')
            ->assertJsonPath('data.transactions.0.credit', 300)
            ->assertJsonPath('data.transactions.0.balance', 700)
            ->assertJsonPath('data.transactions.0.balance_type', 'Dr')
            ->assertJsonPath('data.transactions.1.balance', 200)
            ->assertJsonPath('data.transactions.1.balance_type', 'Cr')
            ->assertJsonPath('data.total_credit', 1200)
            ->assertJsonPath('data.closing_balance', 200)
            ->assertJsonPath('data.closing_balance_type', 'Cr');
    });

    it('validates the account and dates', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/general-ledger?account_id=999999&from_date=2026-02-10&to_date=2026-02-01')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_id', 'to_date']]);
    });
});

describe('trial balance', function () {
    it('balances as of a date', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->bank, 400, '2026-01-15');

        $response = $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/trial-balance?from_date=2026-01-01&to_date=2026-01-31&report_type=as_of_date')
            ->assertOk()
            ->assertJsonPath('data.total_debit', 1400)
            ->assertJsonPath('data.total_credit', 1400);

        $rows = collect($response->json('data.accounts'))->keyBy('account_no');

        expect($rows['1010']['debit'])->toEqual(1000)
            ->and($rows['1020']['credit'])->toEqual(400)
            ->and($rows['4010']['credit'])->toEqual(1000)
            ->and($rows['5030']['debit'])->toEqual(400);
    });

    it('splits a period into opening, movement and closing balances', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 250, '2026-02-10');

        $response = $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/trial-balance?from_date=2026-02-01&to_date=2026-02-28&report_type=as_of_period')
            ->assertOk();

        $cash = collect($response->json('data.accounts'))->firstWhere('account_no', '1010');

        expect($cash['opening_debit'])->toEqual(1000)
            ->and($cash['credit'])->toEqual(250)
            ->and($cash['closing_debit'])->toEqual(750);
    });
});

describe('profit and loss', function () {
    it('nets revenue against expenses', function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 400, '2026-01-15');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/profit-loss')
            ->assertOk()
            ->assertJsonPath('data.total_revenue', 1000)
            ->assertJsonPath('data.total_expense', 400)
            ->assertJsonPath('data.profit_or_loss', 600)
            ->assertJsonPath('data.result', 'Profit: 600.00');
    });
});

describe('balance sheet', function () {
    it('balances assets against liabilities and equity', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/customers', [
            'name' => 'Opening Customer',
            'opening_balance' => 500,
        ])->assertStatus(201);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/balance-sheet')
            ->assertOk()
            ->assertJsonPath('data.total_assets', 500)
            ->assertJsonPath('data.total_liabilities', 0)
            ->assertJsonPath('data.total_equity', 500)
            ->assertJsonPath('data.is_balanced', true);

        expect(ChartOfAccount::query()->where('account_number', '1030')->first()->balance)->toBe(500.0)
            ->and(Customer::query()->where('name', 'Opening Customer')->value('current_balance'))->toEqual('500.00');
    });
});

describe('cash transactions', function () {
    it("lists the selected accounts' journal lines in the date range with totals", function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->cash, 1000, '2026-01-10');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->cash, 300, '2026-02-05');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->bank, 50, '2026-02-06');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/cash-transactions?account_ids[]={$this->cash->id}&from_date=2026-01-01&to_date=2026-02-28")
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.date', '2026-01-10')
            ->assertJsonPath('data.rows.0.ledger_name', $this->cash->name)
            ->assertJsonPath('data.rows.0.debit', 1000)
            ->assertJsonPath('data.rows.1.credit', 300)
            ->assertJsonPath('data.total_debit', 1000)
            ->assertJsonPath('data.total_credit', 300);
    });
});

describe('supplier reports', function () {
    it('brings the balance forward and keeps a running due, skipping reversed entries', function () {
        $supplier = Supplier::factory()->create(['opening_balance' => 100, 'current_balance' => 100]);
        SupplierTransaction::factory()->for($supplier)->create(['credit' => 400, 'debit' => 0, 'transaction_date' => '2026-01-05']);
        SupplierTransaction::factory()->for($supplier)->create(['credit' => 1000, 'debit' => 0, 'transaction_date' => '2026-02-01']);
        SupplierTransaction::factory()->for($supplier)->create(['credit' => 0, 'debit' => 600, 'transaction_date' => '2026-02-10', 'transaction_type' => 'Bill Payment']);
        SupplierTransaction::factory()->for($supplier)->create(['credit' => 999, 'debit' => 0, 'transaction_date' => '2026-02-11', 'status' => false]);
        SupplierTransaction::factory()->create(['credit' => 50, 'transaction_date' => '2026-02-02']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/supplier-statement?supplier_ids[]={$supplier->id}&from_date=2026-02-01&to_date=2026-02-28")
            ->assertOk()
            ->assertJsonPath('data.previous_balance', 500)
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.party_name', $supplier->name)
            ->assertJsonPath('data.rows.0.amount', 1000)
            ->assertJsonPath('data.rows.0.due', 1500)
            ->assertJsonPath('data.rows.1.paid', 600)
            ->assertJsonPath('data.rows.1.due', 900)
            ->assertJsonPath('data.total_amount', 1000)
            ->assertJsonPath('data.total_paid', 600)
            ->assertJsonPath('data.total_due', 900);
    });

    it("lists active suppliers' current balances", function () {
        Supplier::factory()->create(['name' => 'A Supplier', 'opening_balance' => 100, 'current_balance' => 250]);
        Supplier::factory()->create(['name' => 'B Supplier', 'opening_balance' => 0, 'current_balance' => 50]);
        Supplier::factory()->create(['active_status' => false, 'current_balance' => 999]);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/supplier-balances')
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.name', 'A Supplier')
            ->assertJsonPath('data.total_current_balance', 300);
    });

    it('validates the supplier ids', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/supplier-statement?supplier_ids[]=999999')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['supplier_ids.0']]);
    });
});

describe('customer statement', function () {
    it('raises the due with invoices and lowers it with payments', function () {
        $customer = Customer::factory()->create(['opening_balance' => 200, 'current_balance' => 200]);
        CustomerTransaction::factory()->for($customer)->create(['debit' => 500, 'credit' => 0, 'transaction_date' => '2026-03-01']);
        CustomerTransaction::factory()->for($customer)->create(['debit' => 0, 'credit' => 300, 'transaction_date' => '2026-03-05', 'transaction_type' => 'Payment Received']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/customer-statement?customer_ids[]={$customer->id}")
            ->assertOk()
            ->assertJsonPath('data.previous_balance', 200)
            ->assertJsonPath('data.rows.0.due', 700)
            ->assertJsonPath('data.rows.1.paid', 300)
            ->assertJsonPath('data.total_due', 400);
    });
});

describe('purchase and sales reports', function () {
    it('lists purchases by credit account with VAT and net totals', function () {
        $payable = ChartOfAccount::query()->where('account_number', '2010')->first();
        Purchase::factory()->create(['credit_account_id' => $payable->id, 'purchase_date' => '2026-04-02', 'vat_amount' => 10, 'net_total' => 110]);
        Purchase::factory()->create(['credit_account_id' => $payable->id, 'purchase_date' => '2026-04-20', 'vat_amount' => 5, 'net_total' => 55]);
        Purchase::factory()->create(['credit_account_id' => $this->cash->id, 'purchase_date' => '2026-04-03']);
        Purchase::factory()->create(['credit_account_id' => $payable->id, 'purchase_date' => '2026-05-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/expense-details?account_ids[]={$payable->id}&from_date=2026-04-01&to_date=2026-04-30")
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.ledger_name', $payable->name)
            ->assertJsonPath('data.total_vat', 15)
            ->assertJsonPath('data.total_net_amount', 165);
    });

    it('summarises sales against purchases with a running balance', function () {
        Sale::factory()->create(['issue_date' => '2026-05-02', 'net_total' => 1000]);
        Purchase::factory()->create(['purchase_date' => '2026-05-02', 'net_total' => 400]);
        Sale::factory()->create(['issue_date' => '2026-05-10', 'net_total' => 250]);
        Sale::factory()->create(['issue_date' => '2026-06-01', 'net_total' => 999]);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/sales-purchase-summary?from_date=2026-05-01&to_date=2026-05-31')
            ->assertOk()
            ->assertJsonCount(3, 'data.rows')
            ->assertJsonPath('data.rows.0.type', 'Purchase')
            ->assertJsonPath('data.rows.0.balance', -400)
            ->assertJsonPath('data.rows.1.balance', 600)
            ->assertJsonPath('data.rows.2.balance', 850)
            ->assertJsonPath('data.total_sales', 1250)
            ->assertJsonPath('data.total_purchase', 400)
            ->assertJsonPath('data.balance', 850);
    });

    it('requires the summary date range', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/sales-purchase-summary')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['from_date', 'to_date']]);
    });

    it("lists a customer's sales with paid and due totals", function () {
        $customer = Customer::factory()->create();
        Sale::factory()->for($customer)->create(['issue_date' => '2026-06-01', 'total_amount' => 1000, 'discount_amount' => 0, 'vat_amount' => 0, 'net_total' => 1000, 'paid_amount' => 400]);
        Sale::factory()->for($customer)->create(['issue_date' => '2026-06-15', 'total_amount' => 500, 'discount_amount' => 0, 'vat_amount' => 0, 'net_total' => 500, 'paid_amount' => 0]);
        Sale::factory()->create(['issue_date' => '2026-06-10']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/sales?customer_id={$customer->id}&from_date=2026-06-01&to_date=2026-06-30")
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.due_amount', 600)
            ->assertJsonPath('data.totals.net_total', 1500)
            ->assertJsonPath('data.totals.paid_amount', 400)
            ->assertJsonPath('data.totals.due_amount', 1100);
    });
});

describe('bank reconciliation', function () {
    beforeEach(function () {
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->bank, 5000, '2026-03-01');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->bank, 1200, '2026-03-10');
        postIncomeExpense($this->actor, 'income', $this->revenue, $this->bank, 800, '2026-03-30');
        postIncomeExpense($this->actor, 'expense', $this->rent, $this->bank, 300, '2026-03-31');

        $this->bankLines = AccountTransactionDetail::query()
            ->where('chart_of_account_id', $this->bank->id)
            ->orderBy('id')
            ->get();
    });

    it('adjusts the book balance by the deposits and payments not yet cleared', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->patchJson('/api/accounting/reports/bank-reconciliation/cleared', [
                'account_id' => $this->bank->id,
                'line_ids' => $this->bankLines->take(2)->pluck('id')->all(),
                'cleared_date' => '2026-03-15',
            ])
            ->assertOk()
            ->assertJsonPath('data.updated', 2);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/bank-reconciliation?account_id={$this->bank->id}&from_date=2026-03-01&to_date=2026-03-31&statement_balance=3800")
            ->assertOk()
            ->assertJsonCount(4, 'data.transactions')
            ->assertJsonPath('data.transactions.0.is_cleared', true)
            ->assertJsonPath('data.transactions.2.is_cleared', false)
            ->assertJsonPath('data.book_balance', 4300)
            ->assertJsonPath('data.outstanding_deposits', 800)
            ->assertJsonPath('data.outstanding_payments', 300)
            ->assertJsonPath('data.adjusted_balance', 3800)
            ->assertJsonPath('data.difference', 0)
            ->assertJsonPath('data.is_reconciled', true);
    });

    it('treats a line cleared after the statement date as outstanding and keeps older outstanding lines', function () {
        $this->bankLines[0]->update(['cleared_date' => '2026-04-02']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/bank-reconciliation?account_id={$this->bank->id}&from_date=2026-03-20&to_date=2026-03-31&statement_balance=1000")
            ->assertOk()
            ->assertJsonCount(4, 'data.transactions')
            ->assertJsonPath('data.transactions.0.is_cleared', false)
            ->assertJsonPath('data.outstanding_deposits', 5800)
            ->assertJsonPath('data.outstanding_payments', 1500)
            ->assertJsonPath('data.adjusted_balance', 0)
            ->assertJsonPath('data.difference', 1000)
            ->assertJsonPath('data.is_reconciled', false);
    });

    it('leaves the difference out without a statement balance', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/bank-reconciliation?account_id={$this->bank->id}&to_date=2026-03-31")
            ->assertOk()
            ->assertJsonPath('data.statement_balance', null)
            ->assertJsonPath('data.difference', null)
            ->assertJsonPath('data.is_reconciled', null);
    });

    it('unticks cleared lines when no date is sent', function () {
        $this->bankLines[0]->update(['cleared_date' => '2026-03-02']);

        $this->actingAs($this->actor, 'sanctum')
            ->patchJson('/api/accounting/reports/bank-reconciliation/cleared', [
                'account_id' => $this->bank->id,
                'line_ids' => [$this->bankLines[0]->id],
                'cleared_date' => null,
            ])
            ->assertOk();

        expect($this->bankLines[0]->fresh()->cleared_date)->toBeNull();
    });

    it('rejects lines of another account or a cleared date before the entry', function () {
        $revenueLine = AccountTransactionDetail::query()->where('chart_of_account_id', $this->revenue->id)->first();

        $this->actingAs($this->actor, 'sanctum')
            ->patchJson('/api/accounting/reports/bank-reconciliation/cleared', [
                'account_id' => $this->bank->id,
                'line_ids' => [$revenueLine->id],
                'cleared_date' => '2026-03-31',
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['line_ids']]);

        $this->actingAs($this->actor, 'sanctum')
            ->patchJson('/api/accounting/reports/bank-reconciliation/cleared', [
                'account_id' => $this->bank->id,
                'line_ids' => [$this->bankLines[3]->id],
                'cleared_date' => '2026-03-15',
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['cleared_date']]);
    });

    it('only reconciles asset accounts', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/bank-reconciliation?account_id={$this->revenue->id}&to_date=2026-03-31")
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['account_id']]);
    });

    it('requires the account-reports.reconcile permission to tick lines off', function () {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo(Permission::findOrCreate('account-reports.view', 'web'));

        $this->actingAs($viewer, 'sanctum')
            ->getJson("/api/accounting/reports/bank-reconciliation?account_id={$this->bank->id}&to_date=2026-03-31")
            ->assertOk();

        $this->actingAs($viewer, 'sanctum')
            ->patchJson('/api/accounting/reports/bank-reconciliation/cleared', [
                'account_id' => $this->bank->id,
                'line_ids' => [$this->bankLines[0]->id],
                'cleared_date' => '2026-03-31',
            ])
            ->assertStatus(403);
    });
});

describe('work order collections', function () {
    it("lists each work order's billed, collected and due amounts", function () {
        $customer = Customer::factory()->create(['name' => 'WO Customer']);
        $workOrder = WorkOrder::factory()->for($customer)->create(['issue_date' => '2026-05-02', 'total_amount' => 10000, 'retention_percent' => 10, 'status' => 'In Progress']);
        CustomerTransaction::factory()->for($customer)->create(['work_order_id' => $workOrder->id, 'debit' => 6000, 'credit' => 0, 'transaction_date' => '2026-05-05']);
        CustomerTransaction::factory()->for($customer)->create(['work_order_id' => $workOrder->id, 'debit' => 0, 'credit' => 2500, 'transaction_type' => 'Payment Received', 'transaction_date' => '2026-05-10']);
        CustomerTransaction::factory()->for($customer)->create(['work_order_id' => $workOrder->id, 'debit' => 0, 'credit' => 900, 'transaction_type' => 'Payment Received', 'transaction_date' => '2026-05-11', 'status' => false]);
        WorkOrder::factory()->for($customer)->create(['issue_date' => '2026-07-01']);
        WorkOrder::factory()->create(['issue_date' => '2026-05-03']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/work-order-collections?customer_id={$customer->id}&from_date=2026-05-01&to_date=2026-05-31")
            ->assertOk()
            ->assertJsonCount(1, 'data.rows')
            ->assertJsonPath('data.rows.0.work_order_no', $workOrder->work_order_no)
            ->assertJsonPath('data.rows.0.customer_name', 'WO Customer')
            ->assertJsonPath('data.rows.0.retention_amount', 1000)
            ->assertJsonPath('data.rows.0.billed_amount', 6000)
            ->assertJsonPath('data.rows.0.collected_amount', 2500)
            ->assertJsonPath('data.rows.0.due_amount', 7500)
            ->assertJsonPath('data.totals.due_amount', 7500);
    });

    it('filters by status and validates it', function () {
        WorkOrder::factory()->create(['status' => 'Completed']);
        WorkOrder::factory()->create(['status' => 'Pending']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/work-order-collections?status=Completed')
            ->assertOk()
            ->assertJsonCount(1, 'data.rows')
            ->assertJsonPath('data.rows.0.status', 'Completed');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/work-order-collections?status=Unknown')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['status']]);
    });
});

describe('customer collections', function () {
    beforeEach(function () {
        $this->customer = Customer::factory()->create(['name' => 'Monthly Customer', 'opening_balance' => 100]);
        CustomerTransaction::factory()->for($this->customer)->create(['debit' => 400, 'credit' => 0, 'transaction_date' => '2025-12-20']);
        CustomerTransaction::factory()->for($this->customer)->create(['debit' => 1000, 'credit' => 0, 'transaction_date' => '2026-01-05']);
        CustomerTransaction::factory()->for($this->customer)->create(['debit' => 0, 'credit' => 700, 'transaction_type' => 'Payment Received', 'transaction_date' => '2026-01-25']);
        CustomerTransaction::factory()->for($this->customer)->create(['debit' => 0, 'credit' => 300, 'transaction_type' => 'Payment Received', 'transaction_date' => '2026-03-31']);
        CustomerTransaction::factory()->for($this->customer)->create(['debit' => 0, 'credit' => 999, 'transaction_date' => '2026-03-15', 'status' => false]);
    });

    it("summarises each customer's billing, collection and running due per month", function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/customer-collections?period=monthly&customer_ids[]={$this->customer->id}&from_date=2026-01-01&to_date=2026-03-31")
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.period', '2026-01')
            ->assertJsonPath('data.rows.0.customer_name', 'Monthly Customer')
            ->assertJsonPath('data.rows.0.billed_amount', 1000)
            ->assertJsonPath('data.rows.0.collected_amount', 700)
            ->assertJsonPath('data.rows.0.due_amount', 800)
            ->assertJsonPath('data.rows.1.period', '2026-03')
            ->assertJsonPath('data.rows.1.collected_amount', 300)
            ->assertJsonPath('data.rows.1.due_amount', 500)
            ->assertJsonPath('data.total_collected', 1000)
            ->assertJsonPath('data.total_due', 500);
    });

    it('groups the same entries per year', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/accounting/reports/customer-collections?period=yearly&customer_ids[]={$this->customer->id}&from_date=2025-01-01&to_date=2026-12-31")
            ->assertOk()
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.period', '2025')
            ->assertJsonPath('data.rows.0.due_amount', 500)
            ->assertJsonPath('data.rows.1.period', '2026')
            ->assertJsonPath('data.rows.1.billed_amount', 1000)
            ->assertJsonPath('data.rows.1.collected_amount', 1000)
            ->assertJsonPath('data.rows.1.due_amount', 500);
    });

    it('requires the period and date range', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/accounting/reports/customer-collections?period=weekly')
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['period', 'from_date', 'to_date']]);
    });
});
