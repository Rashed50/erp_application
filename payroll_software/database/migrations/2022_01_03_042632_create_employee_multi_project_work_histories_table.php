<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeMultiProjectWorkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_multi_proj_work_hist', function (Blueprint $table) {
            $table->bigInteger('empwh_auto_id');
            $table->integer('emp_id');
            $table->integer('month');
            $table->integer('year');
            $table->integer('project_id');
            $table->integer('total_day');
            $table->integer('total_hour');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_overtime')->default(0);
            $table->integer('paid_amount')->default(0);
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
        Schema::dropIfExists('employee_multi_project_work_histories');
    }
}
