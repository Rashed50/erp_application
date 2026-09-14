<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_details', function (Blueprint $table) {

            $table->increments('ed_auto_id');
            $table->integer('emp_auto_id');
            $table->smallInteger('dept_id');
            $table->smallInteger('last_education_id')->nullable();
            $table->smallInteger('religion_id');
            $table->smallInteger('expertness_rating')->default(0);
            $table->string('country_phone_no')->nullable();
            $table->smallInteger('agc_info_auto_id');
            $table->string('gender')->default("M"); // M = male, F = female
            $table->boolean('is_married')->default(1); // 1 = married, 0 unmarried
            $table->string('blood_group')->nullable();
            $table->string('present_address')->nullable();
            $table->string('ref_employee_id')->nullable();
            $table->string('remarks')->nullable();

            // Last modified table column
            $table->string('educational_papers')->nullable();
            $table->string('blood_group_paper')->nullable();
            $table->string('bg_paper')->nullable();
            $table->text('emp_act_remarks')->nullable();
            $table->text('gosi_number')->nullable()->unique();
            $table->timestamps();
        });
    }
    //php artisan migrate --path=/database/migrations/full_migration_file_name_migration.php


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_details');
    }
}
