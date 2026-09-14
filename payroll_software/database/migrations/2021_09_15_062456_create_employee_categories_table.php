<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Employee Designation Table
        Schema::create('employee_categories', function (Blueprint $table) {
          $table->id('catg_id');
          $table->integer('emp_type_id');
          $table->integer('emp_desig_head_id')->default(1);
          $table->string('catg_name',100);
          $table->boolean('catg_status')->default(1);
          $table->timestamps();
        });


        DB::table('employee_categories')->insert(
            array(
                  'emp_type_id' => 1,
                  'catg_name' => 'Project Manager'
            )
          );
          DB::table('employee_categories')->insert(
            array(
                  'emp_type_id' => 1,
                  'catg_name' => 'Project Incharge'
            )
          );
          DB::table('employee_categories')->insert(
            array(
                  'emp_type_id' => 2,
                  'catg_name' => 'Carpainter'
            )
          );

          DB::table('employee_categories')->insert(
            array(
                  'emp_type_id' => 2,
                  'catg_name' => 'Cleaner'
            )
          );

          DB::table('employee_categories')->insert(
            array(
                  'emp_type_id' => 2,
                  'catg_name' => 'Security Guard'
            )
          );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_categories');
    }
}
