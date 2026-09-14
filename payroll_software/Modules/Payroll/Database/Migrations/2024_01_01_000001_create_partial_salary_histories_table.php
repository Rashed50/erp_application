<?php
// database/migrations/2024_01_01_000001_create_partial_salary_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartialSalaryHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('partial_salary_histories', function (Blueprint $table) {
            $table->increments('psh_auto_id');
            // create column and foreign key relationship with employee_infos table
            $table->foreignId('emp_auto_id')->constrained('employee_infos', 'emp_auto_id')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->integer('amount');
            $table->date('paid_at');
            $table->foreignId('project_id')->constrained('project_infos', 'proj_id')->onDelete('cascade');
          //  $table->unsignedSmallInteger('inserted_by')->nullable();
            $table->foreignId('inserted_by')
                ->nullable() // Usually nullable because the first user can't be created by anyone
                ->constrained('users') // References the 'id' on 'users' table
                ->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // unique index to prevent duplicate entries for the same employee, month, and year
            $table->unique(['emp_auto_id', 'month', 'year'], 'unique_partial_salary_per_month');

        });
    }

    public function down()
    {

        Schema::dropIfExists('partial_salary_histories');
    }
}
