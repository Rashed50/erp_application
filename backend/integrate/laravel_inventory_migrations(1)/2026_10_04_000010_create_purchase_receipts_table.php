<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 50)->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->date('received_date');
            $table->unsignedBigInteger('received_by')->nullable();
            $table->enum('status', ['draft', 'received', 'cancelled'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('received_by')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['warehouse_id', 'received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
