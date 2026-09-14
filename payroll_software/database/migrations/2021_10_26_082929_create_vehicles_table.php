<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('veh_id');
            $table->integer('company_id');
            $table->string('veh_name');
            $table->string('veh_plate_number');
            $table->string('veh_model_number')->nullable();
            $table->string('veh_brand_name');
            $table->date('veh_insurrance_date');
            $table->double('veh_price',20,2);
            $table->string('veh_color')->nullable();
            $table->string('veh_licence_no');
            $table->integer('veh_type_id');
            $table->date('veh_purchase_date');
            $table->date('joining_date')->nullable();
            $table->integer('veh_present_metar')->default(0);
            $table->date('veh_ins_expire_date')->nullable();
            $table->date('veh_ins_renew_date')->nullable();
            $table->string('veh_ins_certificate',100)->nullable();
            $table->date('veh_reg_expire_date')->nullable();
            $table->date('veh_reg_renew_date')->nullable();
            $table->string('veh_reg_certificate',100)->nullable();
            $table->string('veh_photo',100)->nullable();
            $table->string('remarks')->nullable();
            $table->integer('create_by_id');
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
        Schema::dropIfExists('vehicles');
    }
}
