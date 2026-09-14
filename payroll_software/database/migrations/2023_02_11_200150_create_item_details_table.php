<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //InventoryPurchaseRecordDetails
        Schema::create('item_details', function (Blueprint $table) {
            $table->id('item_deta_id');
            $table->integer('itype_id');
            $table->integer('quantity');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            $table->integer('item_name_auto_id');
            $table->integer('item_comp_id');
            $table->integer('ibrand_id');
            $table->unsignedBigInteger('inv_purchase_rec_id');
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('item_det_unit');
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
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
        Schema::dropIfExists('item_details');
    }
}
