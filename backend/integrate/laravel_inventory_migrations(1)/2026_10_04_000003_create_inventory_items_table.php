<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('category_id')->constrained('inventory_categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('inventory_units')->restrictOnDelete();
            $table->enum('item_type', ['asset', 'non_asset'])->default('non_asset');
            $table->enum('track_type', ['quantity', 'serial'])->default('quantity');
            $table->decimal('minimum_stock', 15, 3)->default(0);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['item_type', 'track_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
