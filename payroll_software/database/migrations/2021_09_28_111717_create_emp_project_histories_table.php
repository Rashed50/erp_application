<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpProjectHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_project_histories', function (Blueprint $table) {
            $table->id('eph_id');
            $table->integer('emp_id');
            $table->integer('project_id');
            $table->date('asigned_date');
            $table->date('relesed_date')->nullable();
            $table->integer('create_by_id');
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
        Schema::dropIfExists('emp_project_histories');
    }
}
