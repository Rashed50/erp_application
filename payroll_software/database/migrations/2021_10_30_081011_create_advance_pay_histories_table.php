<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancePayHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_pay_histories', function (Blueprint $table) {
            $table->id('aph_id');
            $table->integer('adv_pay_id');
            $table->date('aph_date');
            $table->integer('aph_month');
            $table->integer('aph_year');
            $table->integer('amount');
            $table->integer('create_by_id');
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
        Schema::dropIfExists('advance_pay_histories');
    }
}
