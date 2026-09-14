<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id');
            $table->integer('adv_purpose_id');
            $table->float('adv_amount');
            $table->integer('installes_month');
            $table->integer('year');
            $table->date('date');
            $table->integer('create_by');
            $table->string('adv_remarks')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('paid_status')->default(0);
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
        Schema::dropIfExists('advance_infos');
    }
}
