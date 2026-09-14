<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_daily_salaries', function (Blueprint $table) {
            // id auto increment
            $table->id();
            // project_id int (assuming it links to a projects table)
            $table->integer('project_id');
            // day, month, year as integers
            $table->integer('day');
            $table->integer('month');
            $table->integer('year');
            // total_salary int
            $table->integer('total_salary');
            // Optional: adds created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_salaries');
    }
};
