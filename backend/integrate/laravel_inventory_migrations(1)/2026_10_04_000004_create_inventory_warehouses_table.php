<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_code', 50)->unique();
            $table->string('name', 200);
            $table->enum('warehouse_type', ['center', 'zonal', 'project_store']);
            $table->foreignId('parent_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('manager_employee_auto_id')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('project_id')
                ->references('proj_id')->on('project_infos')
                ->nullOnDelete();

            $table->foreign('manager_employee_auto_id')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['warehouse_type', 'status']);
            $table->index('zone_id');
            $table->index('project_id');
            $table->index('manager_employee_auto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_warehouses');
    }
};
