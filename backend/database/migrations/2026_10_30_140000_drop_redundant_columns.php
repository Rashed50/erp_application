<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop columns that only repeat what a related row already holds:
     * - salary_payments.employee_id: the salary's employee.
     * - employee_advance_recoveries.employee_id / salary_month: the advance's
     *   employee and the deducting salary's month.
     * - employee_inventory_returns.employee_id / item_id / serial_id: the
     *   issued row's.
     * - asset_returns.asset_id / employee_id: the assignment's.
     * - inventory_assets item, receipt, dates, store, custodian, status and
     *   condition: the serial's (the asset keeps only its tag and cost).
     * - item_names.icatg_id: the sub category's category.
     * - account_transactions.sale_id / purchase_id: the polymorphic source.
     *
     * sale_items / purchase_items keep item_name only for free-text lines; a
     * product line is named by its product.
     */
    public function up(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
        });

        Schema::table('employee_advance_recoveries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
            $table->dropIndex(['salary_month']);
            $table->dropColumn('salary_month');
        });

        Schema::table('employee_inventory_returns', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['serial_id']);
            $table->dropIndex(['employee_id', 'return_date']);
            $table->dropColumn(['employee_id', 'item_id', 'serial_id']);
        });

        Schema::table('asset_returns', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropForeign(['employee_id']);
            $table->dropIndex(['asset_id', 'return_date']);
            $table->dropColumn(['asset_id', 'employee_id']);
        });

        Schema::table('inventory_assets', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['purchase_receipt_item_id']);
            $table->dropForeign(['current_warehouse_id']);
            $table->dropForeign(['current_employee_id']);
            $table->dropIndex(['current_warehouse_id']);
            $table->dropIndex(['current_employee_id']);
            $table->dropIndex(['status', 'condition']);
            $table->dropColumn(['item_id', 'purchase_receipt_item_id', 'purchase_date', 'warranty_expiry', 'current_warehouse_id', 'current_employee_id', 'status', 'condition']);
        });

        Schema::table('item_names', function (Blueprint $table) {
            $table->dropColumn('icatg_id');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sale_id');
            $table->dropConstrainedForeignId('purchase_id');
        });

        foreach (['sale_items', 'purchase_items'] as $itemsTable) {
            Schema::table($itemsTable, function (Blueprint $table) {
                $table->string('item_name')->nullable()->change();
            });

            DB::table($itemsTable)->whereNotNull('product_id')->update(['item_name' => null]);
        }
    }

    /**
     * Reverse the migrations: bring the columns back and fill them from the
     * related rows again.
     */
    public function down(): void
    {
        foreach (['sale_items', 'purchase_items'] as $itemsTable) {
            DB::table($itemsTable)
                ->join('products', 'products.id', '=', "{$itemsTable}.product_id")
                ->whereNull("{$itemsTable}.item_name")
                ->update(["{$itemsTable}.item_name" => DB::raw('products.name')]);

            Schema::table($itemsTable, function (Blueprint $table) {
                $table->string('item_name')->nullable(false)->change();
            });
        }

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->foreignId('sale_id')->nullable()->after('general_particular')->constrained('sales')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->after('sale_id')->constrained('purchases')->nullOnDelete();
        });

        foreach (['sale_id' => 'App\\Models\\Sale', 'purchase_id' => 'App\\Models\\Purchase'] as $column => $type) {
            DB::table('account_transactions')->where('source_type', $type)->update([$column => DB::raw('source_id')]);
        }

        DB::table('account_transactions')
            ->join('supplier_payments', 'supplier_payments.id', '=', 'account_transactions.source_id')
            ->where('account_transactions.source_type', 'App\\Models\\SupplierPayment')
            ->update(['account_transactions.purchase_id' => DB::raw('supplier_payments.purchase_id')]);

        Schema::table('item_names', function (Blueprint $table) {
            $table->integer('icatg_id')->default(0)->after('item_id');
        });

        DB::table('item_names')
            ->join('item_sub_categories', 'item_sub_categories.iscatg_id', '=', 'item_names.iscatg_id')
            ->update(['item_names.icatg_id' => DB::raw('item_sub_categories.icatg_id')]);

        Schema::table('inventory_assets', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->after('id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('purchase_receipt_item_id')->nullable()->after('asset_tag')->constrained('purchase_receipt_items')->nullOnDelete();
            $table->date('purchase_date')->nullable()->after('purchase_cost');
            $table->date('warranty_expiry')->nullable()->after('purchase_date');
            $table->foreignId('current_warehouse_id')->nullable()->after('warranty_expiry')->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('current_employee_id')->nullable()->after('current_warehouse_id')->constrained('employee_info')->nullOnDelete();
            $table->string('status', 30)->default('in_stock')->after('current_employee_id');
            $table->string('condition', 30)->default('new')->after('status');
            $table->index('current_warehouse_id');
            $table->index('current_employee_id');
            $table->index(['status', 'condition']);
        });

        DB::table('inventory_assets')
            ->join('inventory_item_serials', 'inventory_item_serials.id', '=', 'inventory_assets.serial_id')
            ->update(collect(['item_id', 'purchase_receipt_item_id', 'purchase_date', 'warranty_expiry', 'current_warehouse_id', 'current_employee_id', 'status', 'condition'])
                ->mapWithKeys(fn (string $column) => ["inventory_assets.{$column}" => DB::raw("inventory_item_serials.{$column}")])
                ->all());

        Schema::table('asset_returns', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->after('asset_assignment_id')->constrained('inventory_assets')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->after('asset_id')->constrained('employee_info')->restrictOnDelete();
            $table->index(['asset_id', 'return_date']);
        });

        DB::table('asset_returns')
            ->join('asset_assignments', 'asset_assignments.id', '=', 'asset_returns.asset_assignment_id')
            ->update(['asset_returns.asset_id' => DB::raw('asset_assignments.asset_id'), 'asset_returns.employee_id' => DB::raw('asset_assignments.employee_id')]);

        Schema::table('employee_inventory_returns', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('employee_inventory_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('item_id')->nullable()->after('employee_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->after('item_id')->constrained('inventory_item_serials')->nullOnDelete();
            $table->index(['employee_id', 'return_date']);
        });

        DB::table('employee_inventory_returns')
            ->join('employee_inventory', 'employee_inventory.id', '=', 'employee_inventory_returns.employee_inventory_id')
            ->update(collect(['employee_id', 'item_id', 'serial_id'])
                ->mapWithKeys(fn (string $column) => ["employee_inventory_returns.{$column}" => DB::raw("employee_inventory.{$column}")])
                ->all());

        Schema::table('employee_advance_recoveries', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('employee_advance_id')->constrained('employee_info')->restrictOnDelete();
            $table->date('salary_month')->nullable()->index()->after('recovery_date');
        });

        DB::table('employee_advance_recoveries')
            ->join('employee_advances', 'employee_advances.id', '=', 'employee_advance_recoveries.employee_advance_id')
            ->update(['employee_advance_recoveries.employee_id' => DB::raw('employee_advances.employee_id')]);

        DB::table('employee_advance_recoveries')
            ->join('salary_history', 'salary_history.id', '=', 'employee_advance_recoveries.salary_history_id')
            ->update(['employee_advance_recoveries.salary_month' => DB::raw('salary_history.salary_month')]);

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('salary_history_id')->constrained('employee_info')->restrictOnDelete();
        });

        DB::table('salary_payments')
            ->join('salary_history', 'salary_history.id', '=', 'salary_payments.salary_history_id')
            ->update(['salary_payments.employee_id' => DB::raw('salary_history.employee_id')]);
    }
};
