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
        Schema::create('inventory_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('disposal_no', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->date('disposal_date');
            $table->string('reason');
            $table->string('status', 30)->default('draft');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('disposed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disposed_at')->nullable();
            $table->timestamps();

            $table->index(['warehouse_id', 'status']);
            $table->index('disposal_date');
        });

        Schema::create('inventory_disposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained('inventory_disposals')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('item_names', 'item_id')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'serial_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_disposal_items');
        Schema::dropIfExists('inventory_disposals');
    }
};
