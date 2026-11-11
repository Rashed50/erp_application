<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 50)->unique();
            // The store asking for the material, usually a project store.
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->date('request_date');
            $table->date('required_date')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('submitted');
            $table->text('remarks')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'status']);
            $table->index('request_date');
        });

        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->constrained('material_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->decimal('requested_qty', 15, 3);
            $table->decimal('approved_qty', 15, 3)->default(0);
            $table->decimal('purchased_qty', 15, 3)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['material_request_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_items');
        Schema::dropIfExists('material_requests');
    }
};
