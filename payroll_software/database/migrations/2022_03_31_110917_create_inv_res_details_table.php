<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvResDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_record_detailss', function (Blueprint $table) {
            $table->bigIncrements('inv_record_auto_id');
            $table->string('items_details');
            $table->float('percent_of_retention', 10);
            $table->float('percent_of_vat', 10);
            $table->float('rate', 10);
            $table->integer('quantity');
            $table->float('total', 10);
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
        Schema::dropIfExists('inv_res_details');
    }
}
