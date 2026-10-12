<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('inventory_assets')->restrictOnDelete();
            $table->unsignedBigInteger('employee_auto_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->date('assigned_date');
            $table->date('expected_return_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->enum('status', ['assigned', 'returned', 'cancelled'])->default('assigned');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_auto_id')->references('emp_auto_id')->on('employee_infos')->restrictOnDelete();
            $table->foreign('project_id')->references('proj_id')->on('project_infos')->nullOnDelete();
            $table->foreign('assigned_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['employee_auto_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
