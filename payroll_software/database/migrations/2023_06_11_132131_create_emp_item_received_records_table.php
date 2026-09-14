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
        Schema::create('emp_item_received_records', function (Blueprint $table) {
            $table->id('item_received_auto_id');
            $table->integer('emp_auto_id');
            $table->integer('item_auto_id');
            $table->float('approved_qty',5,2);
            $table->float('received_qty',5,2);
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('store_id');
            $table->integer('brand_id');
            $table->integer('item_unit');             
            $table->date('received_date')->nullable();
            $table->date('approved_date')->nullable();
            $table->integer('approved_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->integer('insert_by');
            $table->integer('received_status')->default(1); // 0 = not received , 1= receive
            $table->integer('status')->default(1); // 0 = inactive , 1= active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_item_received_records');
    }
};
