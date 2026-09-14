<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
//  $table = 'project_plots';
 /**
  * Run the migrations.
  */
 public function up(): void
 {
 
   Schema::create('project_plots', function(Blueprint $table ){
       $table->id('pro_plo_auto_id');
       $table->integer('proj_auto_id');
       $table->string('plo_name');
       $table->boolean('status')->default(1);
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
