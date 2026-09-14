<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_approval_records', function (Blueprint $table) {
            $table->id('atten_appro_auto_id');
            $table->integer('project_id');
            $table->integer('month');
            $table->integer('year');
            $table->date('approved_date')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('approval_status')->default(0);
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
        Schema::dropIfExists('attendance_approval_records');
    }
};
