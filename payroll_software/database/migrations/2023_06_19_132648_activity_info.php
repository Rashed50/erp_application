<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  //   $table = 'activity_info';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('activity_info', function(Blueprint $table ){
          $table->id('act_auto_id');
          $table->integer('act_ele_auto_id');
          $table->string('act_name');
          $table->string('act_code')->nullable();
          $table->boolean('status')->default(1);
          $table->integer('approved_by')->nullable();
          $table->integer('update_by')->nullable();
          $table->integer('insert_by');
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
