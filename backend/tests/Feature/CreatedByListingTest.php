<?php

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\FundTransfer;
use App\Models\IncomeExpenseTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\WorkOrder;

dataset('listings', [
    'customers' => ['/api/customers', 'data.customers', Customer::class],
    'suppliers' => ['/api/suppliers', 'data.suppliers', Supplier::class],
    'sales' => ['/api/sales', 'data.sales', Sale::class],
    'purchases' => ['/api/purchases', 'data.purchases', Purchase::class],
    'work orders' => ['/api/work-orders', 'data.work_orders', WorkOrder::class],
    'ledger accounts' => ['/api/ledger-accounts', 'data.accounts', ChartOfAccount::class],
    'income/expenses' => ['/api/income-expenses', 'data.transactions', IncomeExpenseTransaction::class],
    'fund transfers' => ['/api/accounting/internal-fund-transfer/list', 'data.transfers', FundTransfer::class],
    'supplier payments' => ['/api/accounting/purchase/payment/list', 'data.payments', SupplierPayment::class],
    'products' => ['/api/products', 'data.products', Product::class],
]);

it('shows who created each listed record and when', function (string $uri, string $path, string $modelClass) {
    $actor = adminUser();
    $modelClass::factory()->create(['created_by' => $actor->id]);

    $row = $this->actingAs($actor, 'sanctum')->getJson($uri)->assertOk()->json("{$path}.0");

    expect($row['created_by_name'])->toBe($actor->name)
        ->and($row['created_at'])->not->toBeNull();
})->with('listings');
