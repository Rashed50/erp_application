<?php

use App\Models\Customer;
use App\Models\FundTransfer;
use App\Models\IncomeExpenseTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Database\Seeders\DemoDataSeeder;

it('seeds demo data for every accounting module and can be run twice', function () {
    adminUser();

    $this->seed(DemoDataSeeder::class);
    $this->seed(DemoDataSeeder::class);

    expect(Product::count())->toBe(10)
        ->and(Customer::count())->toBe(5)
        ->and(Supplier::count())->toBe(5)
        ->and(Sale::count())->toBe(4)
        ->and(Purchase::count())->toBe(3)
        ->and(SupplierPayment::count())->toBe(2)
        ->and(FundTransfer::count())->toBe(1)
        ->and(IncomeExpenseTransaction::count())->toBe(3)
        ->and(SaleItem::whereNotNull('product_id')->count())->toBe(SaleItem::count())
        ->and(PurchaseItem::whereNotNull('product_id')->count())->toBe(PurchaseItem::count());
});
