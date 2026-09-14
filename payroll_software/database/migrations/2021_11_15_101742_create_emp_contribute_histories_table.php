<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpContributeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_contribute_histories', function (Blueprint $table) {
            $table->id('EmpContHistId');
            $table->unsignedBigInteger('emp_auto_id');
            $table->float('Amount',11,2);
            $table->integer('Month');
            $table->integer('Year');
            $table->unsignedBigInteger('CreateById');
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
        Schema::dropIfExists('emp_contribute_histories');
    }
}
