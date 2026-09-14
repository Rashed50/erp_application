<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_purchases', function (Blueprint $table) {
            $table->id('item_pur_id');
            $table->date('date');
            $table->double('other_cost')->default(0);
            $table->double('rate')->nullable();
            $table->double('total_amount');
            $table->double('paid_total_amount');
            $table->integer('purchase_by_id')->comment('Employee Id');
            $table->integer('create_by_id');
            $table->integer('approve_by_id')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('item_purchases');
    }
}
