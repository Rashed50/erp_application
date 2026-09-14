<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_pays', function (Blueprint $table) {
            $table->id('adv_pay_id');
            $table->integer('emp_id');
            $table->integer('adv_pay_purpose');
            $table->string('adv_pay_remarks')->nullable();
            $table->integer('adv_pay_amount');
            $table->integer('installes_month');
            $table->float('installes_amount');
            $table->double('total_paid',11,2)->default(0);
            $table->date('entry_date');
            $table->integer('entered_id');
            $table->boolean('status')->default(1);
            $table->boolean('isfull_paid')->default(0);
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
        Schema::dropIfExists('advance_pays');
    }
}
