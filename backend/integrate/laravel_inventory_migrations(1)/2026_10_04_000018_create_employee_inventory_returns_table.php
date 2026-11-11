<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_inventory_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_inventory_id')->constrained('employee_inventory')->restrictOnDelete();
            $table->unsignedBigInteger('employee_auto_id');
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->date('return_date');
            $table->enum('return_condition', ['new', 'good', 'fair', 'damaged', 'beyond_repair']);
            $table->string('return_reason', 255)->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_auto_id')->references('emp_auto_id')->on('employee_infos')->restrictOnDelete();
            $table->foreign('received_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['employee_auto_id', 'return_date']);
            $table->index('serial_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_inventory_returns');
    }
};
