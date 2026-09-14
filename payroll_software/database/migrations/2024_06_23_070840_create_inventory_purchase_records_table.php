<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryPurchaseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_purchase_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); // sub store info table id
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->date('received_date');
            $table->string('purchase_by');
            $table->string('purchase_from')->nullable();
            $table->string('chalan_no')->nullable();
            $table->smallInteger('create_by_id');
            $table->smallInteger('update_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_purchase_records');
    }
}
