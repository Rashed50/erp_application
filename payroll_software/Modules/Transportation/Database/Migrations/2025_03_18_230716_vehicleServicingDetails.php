<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_servicing_details', function (Blueprint $table) {
            $table->unsignedBigInteger('vehser_det_auto_id', true);
            $table->unsignedBigInteger('veh_ser_auto_id');
            $table->unsignedBigInteger('ser_nam_auto_id');
            $table->float('qty');
            $table->float('unit_rate');
            $table->float('total_amount');
            $table->enum('service_type',  ['Repair', 'New'])->default('Repair');
            $table->string('remarks')->nullable();
            // Foreign key constraints
            $table->foreign('veh_ser_auto_id')->references('veh_ser_auto_id')->on('vehicle_servicing');
            $table->foreign('ser_nam_auto_id')->references('ser_nam_auto_id')->on('vehicle_servicing_names');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table');

    }
};
