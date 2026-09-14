<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id('da_auto_id');
            $table->string('da_subject');
            $table->text('da_details')->nullable();
            $table->unsignedBigInteger('da_type_id');
            $table->unsignedBigInteger('da_for_emp_id')->nullable();
            $table->unsignedBigInteger('da_created_by');
            $table->unsignedBigInteger('da_responsible_emp')->nullable();
            $table->string('da_status', 50); // Created-1, TransparToOther-5 InProgress-10, Completed-15
            $table->integer('da_progress')->default(0)->comment('0 to 100'); 
            $table->text('da_status_remarks')->nullable();
            $table->string('da_attached_file')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_activities');
    }
};
