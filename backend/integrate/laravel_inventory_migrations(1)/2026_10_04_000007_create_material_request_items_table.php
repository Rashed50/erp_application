<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->constrained('material_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->decimal('requested_qty', 15, 3);
            $table->decimal('approved_qty', 15, 3)->default(0);
            $table->decimal('purchased_qty', 15, 3)->default(0);
            $table->decimal('issued_qty', 15, 3)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['material_request_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_request_items');
    }
};
