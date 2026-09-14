<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyWorkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_work_histories', function (Blueprint $table) {
            $table->id('day_work_id');
            $table->integer('emp_id');
            $table->date('entry_date',2);
            $table->integer('month');
            $table->string('year',4);
            $table->string('work_hours',2);
            $table->integer('entered_id');
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
        Schema::dropIfExists('daily_work_histories');
    }
}
