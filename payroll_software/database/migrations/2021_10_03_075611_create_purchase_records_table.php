<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_records', function (Blueprint $table) {
            $table->id('pr_record_id');
            $table->integer('itype_id');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            $table->integer('quantity');
            $table->integer('rate');
            $table->integer('amount');
            $table->integer('item_purchase_id');
            $table->integer('create_by_id');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('purchase_records');
    }
}
