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
        Schema::create('daily_attend_summaries', function (Blueprint $table) {
            $table->id('das_auto_id');
            $table->smallInteger('project_id');
            $table->smallInteger('total_emp');
            $table->smallInteger('total_present');
            $table->smallInteger('total_absent');
            $table->date('attendance_date');
            $table->tinyInteger('day');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->boolean('is_night_shift');   // 0 day shift , 1= night shift
            $table->float('total_basic_hours')->default(0); 
            $table->float('total_ot')->default(0);     

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attend_summaries');
    }
};
