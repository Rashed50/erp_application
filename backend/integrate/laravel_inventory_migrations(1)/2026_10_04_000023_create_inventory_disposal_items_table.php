<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_disposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_id')->constrained('inventory_disposals')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('inventory_item_serials')->nullOnDelete();
            $table->decimal('quantity', 15, 3)->default(1);
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index(['item_id', 'serial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_disposal_items');
    }
};
