<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->string('serial_number', 150)->unique();
            $table->string('asset_tag', 100)->nullable()->unique();
            $table->string('barcode', 150)->nullable()->unique();
            $table->foreignId('purchase_receipt_item_id')->nullable()->constrained('purchase_receipt_items')->nullOnDelete();
            $table->foreignId('current_location_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->unsignedBigInteger('current_employee_auto_id')->nullable();
            $table->enum('status', ['in_stock', 'assigned', 'under_repair', 'damaged', 'lost', 'disposed', 'retired'])->default('in_stock');
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'beyond_repair'])->default('new');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->timestamps();

            $table->foreign('current_employee_auto_id')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['item_id', 'status']);
            $table->index('current_location_id');
            $table->index('current_employee_auto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_serials');
    }
};
