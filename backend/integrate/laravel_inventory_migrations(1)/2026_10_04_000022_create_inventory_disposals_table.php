<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_no', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->date('disposal_date');
            $table->string('reason', 255);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('disposed_by')->nullable();
            $table->enum('status', ['draft', 'approved', 'disposed', 'cancelled'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();
            $table->foreign('disposed_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['warehouse_id', 'status']);
            $table->index('disposal_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_disposals');
    }
};
