<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\FundTransferService;
use App\Services\IncomeExpenseTransactionService;
use App\Services\ProductService;
use App\Services\PurchaseService;
use App\Services\SaleService;
use App\Services\SupplierPaymentService;
use App\Services\SupplierService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Demo records for every accounting module. Everything goes through the
 * application services, so ledgers and account balances stay consistent.
 *
 * Run with: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Has its own "already seeded" check, so it also runs on databases
        // that got the accounting demo before the HR module existed.
        $this->call(HrDemoDataSeeder::class);

        if (Customer::query()->where('email', 'demo.customer1@example.com')->exists()) {
            $this->command?->warn('Demo data already exists, skipping.');

            return;
        }

        $this->call(ChartOfAccountSeeder::class);

        $admin = User::query()->where('email', env('SUPER_ADMIN_EMAIL', 'superadmin@example.com'))->first();
        if ($admin) {
            Auth::login($admin);
        }

        DB::transaction(function () {
            $products = $this->seedProducts();
            $customers = $this->seedCustomers();
            $suppliers = $this->seedSuppliers();

            $cash = $this->account('1010');
            $bank = $this->account('1020');

            $this->seedSales($customers, $products, $cash);
            $this->seedPurchases($suppliers, $products, $cash);

            app(FundTransferService::class)->create([
                'credit_account_id' => $cash,
                'debit_account_id' => $bank,
                'receipt_no' => 'DEMO-FT-001',
                'transfer_date' => now()->subDays(2)->toDateString(),
                // Keeps Cash in Hand positive after the demo receipts, payments and expenses.
                'amount' => 2500,
                'bank_charge' => 0,
                'vat' => 0,
                'remarks' => 'Demo cash deposit to bank',
            ]);

            $incomeExpenseService = app(IncomeExpenseTransactionService::class);
            foreach ([['5040', 1200, 'Electricity bill'], ['5050', 450, 'Office stationery'], ['5070', 800, 'Truck rent']] as $index => [$accountNumber, $amount, $description]) {
                $incomeExpenseService->create([
                    'type' => 'expense',
                    'income_expense_account_id' => $this->account($accountNumber),
                    'payment_account_id' => $cash,
                    'amount' => $amount,
                    'transaction_date' => now()->subDays($index + 1)->toDateString(),
                    'reference_no' => 'DEMO-EXP-00'.($index + 1),
                    'description' => $description,
                ]);
            }
        });

        Auth::logout();
    }

    /**
     * @return array<int, Product>
     */
    private function seedProducts(): array
    {
        $names = [
            'Portland Cement (50kg Bag)', 'Steel Rebar 12mm', 'Steel Rebar 16mm', 'Red Clay Brick',
            'Concrete Block 20cm', 'River Sand (m³)', 'Crushed Stone 20mm (m³)', 'Plywood Sheet 18mm',
            'PVC Pipe 4"', 'Electrical Cable 2.5mm',
        ];

        return collect($names)->map(fn (string $name, int $index) => app(ProductService::class)->create([
            'name' => $name,
            'code' => 'P-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
        ]))->all();
    }

    /**
     * @return array<int, Customer>
     */
    private function seedCustomers(): array
    {
        $names = ['Al Noor Contracting', 'Gulf Star Builders', 'Desert Rose Construction', 'Blue Line Developers', 'Crescent Engineering'];

        return collect($names)->map(fn (string $name, int $index) => app(CustomerService::class)->create([
            'name' => $name,
            'email' => 'demo.customer'.($index + 1).'@example.com',
            'phone' => '+96650000000'.($index + 1),
            'address' => 'Riyadh, Saudi Arabia',
            'vat_no' => 'VAT-10000'.($index + 1),
            'payment_term' => 30,
            'contact_person' => 'Contact '.($index + 1),
            'country' => 'Saudi Arabia',
        ]))->all();
    }

    /**
     * @return array<int, Supplier>
     */
    private function seedSuppliers(): array
    {
        $names = ['Saudi Cement Co.', 'Al Rajhi Steel', 'National Bricks Factory', 'Eastern Timber Supply', 'Modern Electric Trading'];

        return collect($names)->map(fn (string $name, int $index) => app(SupplierService::class)->create([
            'name' => $name,
            'email' => 'demo.supplier'.($index + 1).'@example.com',
            'phone' => '+96655000000'.($index + 1),
            'address' => 'Dammam, Saudi Arabia',
            'vat_no' => 'VAT-20000'.($index + 1),
            'payment_term' => 30,
            'contact_person' => 'Supplier Contact '.($index + 1),
        ]))->all();
    }

    /**
     * @param  array<int, Customer>  $customers
     * @param  array<int, Product>  $products
     */
    private function seedSales(array $customers, array $products, int $cashAccountId): void
    {
        $saleService = app(SaleService::class);

        foreach ([[0, [0, 1], 6000], [1, [3, 4, 5], 3000], [2, [7, 8, 9], 0], [3, [2, 6], 0]] as $index => [$customerIndex, $productIndexes, $paidAmount]) {
            $sale = $saleService->create([
                'customer_id' => $customers[$customerIndex]->id,
                'invoice_number' => 'DEMO-SINV-00'.($index + 1),
                'description' => 'Demo sale',
                'issue_date' => now()->subDays(10 - $index)->toDateString(),
                'due_date' => now()->addDays(20)->toDateString(),
                'items' => collect($productIndexes)->map(fn (int $productIndex) => [
                    'product_id' => $products[$productIndex]->id,
                    'item_name' => $products[$productIndex]->name,
                    'qty' => 50 + ($productIndex * 10),
                    'unit_price' => 25 + ($productIndex * 5),
                    'discount' => 0,
                    'vat' => 0,
                ])->all(),
            ]);

            if ($paidAmount > 0) {
                $saleService->recordPayment($sale, [
                    'payment_account_id' => $cashAccountId,
                    'amount' => $paidAmount,
                    'payment_date' => now()->subDays(5)->toDateString(),
                    'notes' => 'Demo payment received',
                ]);
            }
        }
    }

    /**
     * @param  array<int, Supplier>  $suppliers
     * @param  array<int, Product>  $products
     */
    private function seedPurchases(array $suppliers, array $products, int $cashAccountId): void
    {
        $purchaseService = app(PurchaseService::class);
        $paymentService = app(SupplierPaymentService::class);

        $purchases = [
            [0, 0, 200, 18, 2000],
            [1, 1, 100, 40, 1500],
            [2, 3, 5000, 0.8, 0],
        ];

        foreach ($purchases as $index => [$supplierIndex, $productIndex, $qty, $unitPrice, $paidAmount]) {
            $purchase = $purchaseService->create([
                'supplier_id' => $suppliers[$supplierIndex]->id,
                'purchase_type' => 'product',
                'invoice_number' => 'DEMO-PINV-00'.($index + 1),
                'description' => 'Demo purchase',
                'issue_date' => now()->subDays(12 - $index)->toDateString(),
                'purchase_date' => now()->subDays(12 - $index)->toDateString(),
                'items' => [['product_id' => $products[$productIndex]->id, 'item_name' => $products[$productIndex]->name, 'qty' => $qty, 'unit_price' => $unitPrice, 'discount' => 0, 'vat' => 0]],
            ]);

            if ($paidAmount > 0) {
                $paymentService->create([
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'payment_account_id' => $cashAccountId,
                    'payment_date' => now()->subDays(3)->toDateString(),
                    'bill_amount' => $paidAmount,
                    'bank_charge' => 0,
                    'remarks' => 'Demo supplier payment',
                ]);
            }
        }
    }

    private function account(string $accountNumber): int
    {
        return ChartOfAccount::query()->where('account_number', $accountNumber)->valueOrFail('id');
    }
}
