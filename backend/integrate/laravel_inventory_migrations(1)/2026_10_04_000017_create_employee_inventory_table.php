<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_auto_id');
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->date('issue_date');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->enum('status', ['issued', 'returned', 'lost', 'damaged', 'disposed'])->default('issued');
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'beyond_repair'])->default('good');
            $table->date('expected_return_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_auto_id')->references('emp_auto_id')->on('employee_infos')->restrictOnDelete();
            $table->foreign('project_id')->references('proj_id')->on('project_infos')->nullOnDelete();
            $table->foreign('issued_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['employee_auto_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('serial_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_inventory');
    }
};
