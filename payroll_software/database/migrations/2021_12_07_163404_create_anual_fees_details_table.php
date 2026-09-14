<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnualFeesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anual_fees_details', function (Blueprint $table) {
            $table->id('anu_fee_id');
            $table->string('fee_title');
            $table->float('amount');
            $table->text('remarks');
            $table->integer('fee_year');
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
        Schema::dropIfExists('anual_fees_details');
    }
}
