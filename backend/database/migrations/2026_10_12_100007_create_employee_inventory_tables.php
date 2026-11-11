<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Non-asset items issued to employees (consumables, tools), and the asset
     * register's custody history (assignments / returns).
     */
    public function up(): void
    {
        Schema::create('employee_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->decimal('returned_qty', 15, 3)->default(0);
            $table->date('issue_date');
            $table->date('expected_return_date')->nullable();
            $table->string('status', 30)->default('issued');
            $table->string('condition', 30)->default('good');
            $table->text('remarks')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
        });

        Schema::create('employee_inventory_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_inventory_id')->constrained('employee_inventory')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->foreignId('destination_warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->date('return_date');
            $table->string('return_condition', 30);
            $table->string('return_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'return_date']);
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('inventory_assets')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            // The store the asset left from.
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->date('assigned_date');
            $table->date('expected_return_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->string('status', 30)->default('assigned');
            $table->text('remarks')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index('asset_id');
        });

        Schema::create('asset_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_assignment_id')->constrained('asset_assignments')->restrictOnDelete();
            $table->foreignId('asset_id')->constrained('inventory_assets')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('destination_warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->date('return_date');
            $table->string('condition', 30);
            $table->string('return_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_id', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_returns');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('employee_inventory_returns');
        Schema::dropIfExists('employee_inventory');
    }
};
