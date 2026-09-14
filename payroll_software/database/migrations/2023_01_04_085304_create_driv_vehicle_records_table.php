<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivVehicleRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driv_vehicle_records', function (Blueprint $table) {
            $table->id('driv_veh_auto_id');
            $table->integer('driv_auto_id');
            $table->integer('veh_auto_id');
            $table->integer('project_id');
            $table->date('assign_date');
            $table->date('release_date')->nullable();
            $table->integer('insert_by');
            $table->integer('update_by')->nullable();
            $table->boolean('status', [0,1])->default(1);
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
        Schema::dropIfExists('driv_vehicle_records');
    }
}
