<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeInOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_in_outs', function (Blueprint $table) {
            $table->id('emp_io_id');
            $table->integer('proj_id');
            $table->integer('emp_id');
            $table->integer('emp_io_date');
            $table->integer('emp_io_month');
            $table->integer('emp_io_year');
            $table->boolean('emp_io_shift')->default(0);
            $table->float('emp_io_entry_time', 2);
            $table->float('emp_io_out_time', 2)->default(0);
            $table->float('over_time')->default(0);
            $table->float('daily_work_hours', 2)->default(0);
            $table->date('emp_io_entry_date');
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
        Schema::dropIfExists('employee_in_outs');
    }
}
