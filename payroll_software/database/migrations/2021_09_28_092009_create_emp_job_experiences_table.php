<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpJobExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_job_experiences', function (Blueprint $table) {
            $table->id('ejex_id');
            $table->integer('emp_id');
            $table->string('ejex_title',100);
            $table->date('starting_date');
            $table->date('end_date');
            $table->string('company_name',100);
            $table->string('designation',100);
            $table->text('responsibity');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('emp_job_experiences');
    }
}
