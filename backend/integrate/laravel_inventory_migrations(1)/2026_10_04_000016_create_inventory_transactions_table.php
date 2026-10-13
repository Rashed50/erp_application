<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no', 60)->unique();
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->enum('transaction_type', [
                'purchase', 'transfer_out', 'transfer_in', 'employee_issue',
                'employee_return', 'adjustment_in', 'adjustment_out', 'disposal'
            ]);
            $table->decimal('quantity', 15, 3)->default(0);
            $table->foreignId('from_location_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->dateTime('transaction_date');
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('performed_by')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['item_id', 'transaction_type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('transaction_date');
            $table->index('serial_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
