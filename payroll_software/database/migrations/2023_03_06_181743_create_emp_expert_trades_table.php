<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpExpertTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_expert_trades', function (Blueprint $table) {
            $table->id('emp_exp_trad_id');
            $table->integer('emp_auto_id');
            $table->integer('catg_id');
            $table->integer('insert_by')->nullable();
            $table->integer('update_by');
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
        Schema::dropIfExists('emp_expert_trades');
    }
}
