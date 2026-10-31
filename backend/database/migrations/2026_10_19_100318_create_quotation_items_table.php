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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            // No product/inventory catalog exists yet in this application, so the
            // item is a free-text line description rather than a catalog FK
            // (same reasoning as purchase_items.item_name).
            $table->string('item_name');
            $table->string('description')->nullable();
            $table->string('unit_id')->nullable()->constrained('inventory_units')->cascadeOnDelete();
            $table->decimal('count', 7, 2)->default(1);
            $table->decimal('weight', 7, 2)->default(1);
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
