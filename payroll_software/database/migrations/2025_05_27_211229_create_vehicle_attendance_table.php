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
    {   // 2025_05_27_211229_create_vehicle_attendance_table
       Schema::create('vehicle_attendances', function (Blueprint $table) {
            $table->id('veh_atten_auto_id');
            $table->date('atten_date');
            $table->string('atten_day');
            $table->string('atten_month');
            $table->string('atten_year');
            $table->unsignedBigInteger('working_project_id');
            $table->unsignedBigInteger('veh_auto_id');
            $table->string('driver_name')->nullable();
            $table->string('driver_iqama');
            $table->string('driver_phone_no')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('vehicle_attendance');
    }
};
