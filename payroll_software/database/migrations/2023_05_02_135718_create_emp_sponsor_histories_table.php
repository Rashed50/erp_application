<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpSponsorHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_sponsor_histories', function (Blueprint $table) {
            $table->id('emp_spon_his_id');
            $table->integer('emp_auto_id');
            $table->integer('previous_spon_id');
            $table->integer('new_spon_id');
            $table->integer('inserted_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('emp_sponsor_histories');
    }
}
