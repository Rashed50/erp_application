<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * inventory_stocks is the current per-store snapshot; inventory_transactions
     * is the movement ledger behind it. Serial-tracked units live in
     * inventory_item_serials, and asset items additionally get an
     * inventory_assets register row per unit.
     */
    public function up(): void
    {
        Schema::create('inventory_item_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->string('serial_number', 150)->unique();
            $table->string('barcode', 150)->nullable()->unique();
            $table->foreignId('purchase_receipt_item_id')->nullable()->constrained('purchase_receipt_items')->nullOnDelete();
            $table->foreignId('current_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('current_employee_id')->nullable()->constrained('employee_info')->nullOnDelete();
            $table->string('status', 30)->default('in_stock');
            $table->string('condition', 30)->default('new');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'status']);
            $table->index('current_warehouse_id');
            $table->index('current_employee_id');
        });

        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('reserved_quantity', 15, 3)->default(0);
            $table->decimal('available_quantity', 15, 3)->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'item_id']);
            $table->index('item_id');
        });

        Schema::create('inventory_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->unique()->constrained('inventory_item_serials')->restrictOnDelete();
            $table->string('asset_tag', 100)->unique();
            $table->foreignId('purchase_receipt_item_id')->nullable()->constrained('purchase_receipt_items')->nullOnDelete();
            $table->decimal('purchase_cost', 18, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('current_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('current_employee_id')->nullable()->constrained('employee_info')->nullOnDelete();
            $table->string('status', 30)->default('in_stock');
            $table->string('condition', 30)->default('new');
            $table->timestamps();

            $table->index(['status', 'condition']);
            $table->index('current_warehouse_id');
            $table->index('current_employee_id');
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no', 60)->unique();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->string('transaction_type', 30);
            $table->decimal('quantity', 15, 3)->default(0);
            $table->foreignId('from_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('to_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employee_info')->nullOnDelete();
            $table->nullableMorphs('reference');
            $table->dateTime('transaction_date');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'transaction_type']);
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_assets');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_item_serials');
    }
};
