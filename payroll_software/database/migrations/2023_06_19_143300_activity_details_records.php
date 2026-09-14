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
      Schema::create('activity_details_records', function(Blueprint $table ){
          $table->id('act_det_rec_auto_id');
          $table->integer('proj_auto_id');
          $table->integer('pro_plo_auto_id');
          $table->integer('act_ele_auto_id');
          $table->integer('act_nam_auto_id');
          $table->boolean('working_shift')->default(1);
          $table->integer('total_worker');
          $table->integer('daily_hours');
          $table->integer('total_working_hours');
          $table->integer('emp_auto_id');
          $table->date('working_date');
          $table->boolean('status')->default(1); // 1 active , 0 inactive
          $table->integer('approved_by')->nullable();
          $table->integer('update_by')->nullable();
          $table->integer('insert_by');
          $table->string('remarks')->nullable();
          $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
