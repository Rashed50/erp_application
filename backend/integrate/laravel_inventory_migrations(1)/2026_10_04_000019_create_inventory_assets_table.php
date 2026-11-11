<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('serial_id')->unique()->constrained('inventory_item_serials')->restrictOnDelete();
            $table->string('asset_tag', 100)->unique();
            $table->foreignId('purchase_receipt_item_id')->nullable()->constrained('purchase_receipt_items')->nullOnDelete();
            $table->decimal('purchase_cost', 18, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('current_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->unsignedBigInteger('current_employee_auto_id')->nullable();
            $table->enum('status', ['in_stock', 'assigned', 'under_repair', 'damaged', 'lost', 'disposed', 'retired'])->default('in_stock');
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'beyond_repair'])->default('new');
            $table->timestamps();

            $table->foreign('current_employee_auto_id')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['status', 'condition']);
            $table->index('current_warehouse_id');
            $table->index('current_employee_auto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_assets');
    }
};
