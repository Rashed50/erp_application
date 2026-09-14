<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleFineRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_fine_records', function (Blueprint $table) {
            $table->id('vfr_auto_id');
            $table->integer('veh_id');
            $table->integer('employee_id');
            $table->date('date');
            $table->string('amount');
            $table->string('remarks')->nullable();
            $table->integer('entered_id');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('vehicle_fine_records');
    }
}
