<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_no', 50)->unique();
            $table->foreignId('from_warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->foreignId('to_warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->date('transfer_date');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->enum('status', ['draft', 'requested', 'approved', 'in_transit', 'received', 'cancelled'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();
            $table->foreign('approved_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();
            $table->foreign('received_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['from_warehouse_id', 'status']);
            $table->index(['to_warehouse_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};
