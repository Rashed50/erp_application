<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('inventory_assets')->restrictOnDelete();
            $table->unsignedBigInteger('employee_auto_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('return_date');
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'beyond_repair']);
            $table->string('return_reason', 255)->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_auto_id')->references('emp_auto_id')->on('employee_infos')->restrictOnDelete();
            $table->foreign('project_id')->references('proj_id')->on('project_infos')->nullOnDelete();
            $table->foreign('received_by')->references('emp_auto_id')->on('employee_infos')->nullOnDelete();

            $table->index(['employee_auto_id', 'return_date']);
            $table->index(['asset_id', 'return_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_returns');
    }
};
