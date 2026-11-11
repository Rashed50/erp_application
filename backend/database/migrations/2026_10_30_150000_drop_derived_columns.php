<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Totals and states that are worked out from other columns or rows, so
     * the models now compute them instead of storing a copy:
     * - inventory_stocks.available_quantity: quantity - reserved_quantity.
     * - fund_transfers.total_amount: amount + bank_charge + vat.
     * - supplier_payments.total_amount: bill_amount + bank_charge.
     * - salary_history.paid_amount: the salary payments.
     * - employee_advances.recovered_amount / status: the recoveries.
     * - employee_inventory.returned_qty / status: the returns.
     * - purchase_order_items.received_qty: the receipt lines.
     * - material_request_items.purchased_qty: the request's purchase orders.
     * - customers / suppliers current_balance: opening balance + active
     *   ledger entries.
     * - sales / purchases total_amount, discount_amount, vat_amount,
     *   net_total: the line items plus the overall discount and VAT.
     * - chart_of_accounts.sibling_level: the depth below the parents.
     *
     * @var array<string, array<int, string>>
     */
    private array $columns = [
        'inventory_stocks' => ['available_quantity'],
        'fund_transfers' => ['total_amount'],
        'supplier_payments' => ['total_amount'],
        'salary_history' => ['paid_amount'],
        'employee_advances' => ['recovered_amount', 'status'],
        'employee_inventory' => ['returned_qty', 'status'],
        'purchase_order_items' => ['received_qty'],
        'material_request_items' => ['purchased_qty'],
        'customers' => ['current_balance'],
        'suppliers' => ['current_balance'],
        'sales' => ['total_amount', 'discount_amount', 'vat_amount', 'net_total'],
        'purchases' => ['total_amount', 'discount_amount', 'vat_amount', 'net_total'],
        'chart_of_accounts' => ['sibling_level'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        // The composite index also serves the employee_id foreign key.
        Schema::table('employee_inventory', function (Blueprint $table) {
            $table->index('employee_id');
            $table->dropIndex(['employee_id', 'status']);
        });

        foreach ($this->columns as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations: bring the columns back and fill them from the
     * rows they are worked out from.
     */
    public function down(): void
    {
        Schema::table('inventory_stocks', fn (Blueprint $table) => $table->decimal('available_quantity', 15, 3)->default(0)->after('reserved_quantity'));
        DB::table('inventory_stocks')->update(['available_quantity' => DB::raw('quantity - reserved_quantity')]);

        Schema::table('fund_transfers', fn (Blueprint $table) => $table->decimal('total_amount', 15, 2)->default(0)->after('vat'));
        DB::table('fund_transfers')->update(['total_amount' => DB::raw('amount + bank_charge + vat')]);

        Schema::table('supplier_payments', fn (Blueprint $table) => $table->decimal('total_amount', 15, 2)->default(0)->after('bank_charge'));
        DB::table('supplier_payments')->update(['total_amount' => DB::raw('bill_amount + bank_charge')]);

        Schema::table('salary_history', fn (Blueprint $table) => $table->decimal('paid_amount', 15, 2)->default(0)->after('net_salary'));
        DB::table('salary_history')->update(['paid_amount' => DB::raw('(select coalesce(sum(amount), 0) from salary_payments where salary_payments.salary_history_id = salary_history.id)')]);

        $recovered = '(select coalesce(sum(amount), 0) from employee_advance_recoveries where employee_advance_recoveries.employee_advance_id = employee_advances.id)';
        Schema::table('employee_advances', function (Blueprint $table) {
            $table->decimal('recovered_amount', 15, 2)->default(0)->after('remarks');
            $table->string('status', 20)->default('Running')->index()->after('recovered_amount');
        });
        DB::table('employee_advances')->update(['recovered_amount' => DB::raw($recovered)]);
        DB::table('employee_advances')->whereColumn('recovered_amount', '>=', 'amount')->update(['status' => 'Completed']);

        Schema::table('employee_inventory', function (Blueprint $table) {
            $table->decimal('returned_qty', 15, 3)->default(0)->after('quantity');
            $table->string('status', 30)->default('issued')->after('expected_return_date');
            $table->index(['employee_id', 'status']);
            $table->dropIndex(['employee_id']);
        });
        DB::table('employee_inventory')->update(['returned_qty' => DB::raw('(select coalesce(sum(quantity), 0) from employee_inventory_returns where employee_inventory_returns.employee_inventory_id = employee_inventory.id)')]);
        DB::table('employee_inventory')->whereColumn('returned_qty', '>=', 'quantity')->update(['status' => 'returned']);

        Schema::table('purchase_order_items', fn (Blueprint $table) => $table->decimal('received_qty', 15, 3)->default(0)->after('quantity'));
        DB::table('purchase_order_items')->update(['received_qty' => DB::raw('(select coalesce(sum(quantity), 0) from purchase_receipt_items where purchase_receipt_items.purchase_order_item_id = purchase_order_items.id)')]);

        Schema::table('material_request_items', fn (Blueprint $table) => $table->decimal('purchased_qty', 15, 3)->default(0)->after('approved_qty'));
        DB::table('material_request_items')->update(['purchased_qty' => DB::raw('least(approved_qty, (select coalesce(sum(purchase_order_items.quantity), 0) from purchase_order_items join purchase_orders on purchase_orders.id = purchase_order_items.purchase_order_id where purchase_orders.material_request_id = material_request_items.material_request_id and purchase_order_items.item_id = material_request_items.item_id))')]);

        foreach (['customers' => 'debit - credit', 'suppliers' => 'credit - debit'] as $tableName => $movement) {
            $transactions = $tableName === 'customers' ? 'customer_transactions' : 'supplier_transactions';
            $foreignKey = $tableName === 'customers' ? 'customer_id' : 'supplier_id';

            Schema::table($tableName, fn (Blueprint $table) => $table->decimal('current_balance', 15, 2)->default(0)->after('opening_balance'));
            DB::table($tableName)->update(['current_balance' => DB::raw("opening_balance + (select coalesce(sum({$movement}), 0) from {$transactions} where {$transactions}.{$foreignKey} = {$tableName}.id and {$transactions}.status = 1)")]);
        }

        foreach (['sales' => ['sale_items', 'sale_id'], 'purchases' => ['purchase_items', 'purchase_id']] as $tableName => [$items, $foreignKey]) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('vat_amount', 15, 2)->default(0);
                $table->decimal('net_total', 15, 2)->default(0);
            });

            $sum = fn (string $expression) => "(select coalesce(sum({$expression}), 0) from {$items} where {$items}.{$foreignKey} = {$tableName}.id)";
            DB::table($tableName)->update([
                'total_amount' => DB::raw($sum('qty * unit_price')),
                'discount_amount' => DB::raw($sum('discount').' + overall_discount'),
                'vat_amount' => DB::raw($sum('vat').' + overall_vat'),
            ]);
            DB::table($tableName)->update(['net_total' => DB::raw('total_amount - discount_amount + vat_amount')]);
        }

        Schema::table('chart_of_accounts', fn (Blueprint $table) => $table->unsignedTinyInteger('sibling_level')->default(0)->after('account_number'));

        // Top-level accounts stay at 0; each pass sets the next level down.
        $parentIds = DB::table('chart_of_accounts')->whereNull('parent_id')->pluck('id');

        for ($level = 1; $parentIds->isNotEmpty() && $level < 20; $level++) {
            DB::table('chart_of_accounts')->whereIn('parent_id', $parentIds)->update(['sibling_level' => $level]);
            $parentIds = DB::table('chart_of_accounts')->whereIn('parent_id', $parentIds)->pluck('id');
        }
    }
};
