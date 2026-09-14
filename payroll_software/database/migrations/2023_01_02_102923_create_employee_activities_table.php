<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_activities', function (Blueprint $table) {
            $table->id('emp_act_auto_id');
            $table->integer('emp_auto_id');
            $table->string('remarks');
            $table->integer('emp_act_type_id');
            $table->integer('job_status');
            $table->integer('create_by_id');
            $table->date('create_at');
            $table->string('description', 300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_activities');
    }
}
