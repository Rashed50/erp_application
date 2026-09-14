<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancePayRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_pay_records', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id');
            $table->integer('adv_purpose_id');
            $table->float('adv_amount');
            $table->integer('month');
            $table->integer('year');
            $table->date('date');
            $table->integer('create_by');
            $table->string('adv_remarks')->nullable();
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
        Schema::dropIfExists('advance_pay_records');
    }
}
