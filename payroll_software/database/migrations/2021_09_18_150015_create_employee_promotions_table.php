<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_promotions', function (Blueprint $table) {
            $table->id('emp_prom_id');
            $table->integer('emp_id');
            $table->integer('designation_id');
            $table->integer('new_designation_id');
            $table->integer('entered_id');
            $table->float('basic_amount',11,2)->default(0);
            $table->float('house_rent',11,2)->default(0);
            $table->float('hourly_rent',11,2)->default(0);
            $table->float('food_allowance',11,2)->default(0);
            $table->integer('mobile_allowance')->default(0);
            $table->integer('medical_allowance')->default(0);
            $table->integer('local_travel_allowance')->default(0);
            $table->integer('conveyance_allowance')->default(0);
            $table->float('others1',11,2)->default(0);
            $table->string('prom_by');
            $table->date('prom_date');
            $table->string('prom_apprv_documents',100)->nullable();
            $table->string('prom_remarks')->nullable();
            $table->integer('no_of_increament')->default(0);
            $table->integer('prom_apprv_by')->nullable();
            $table->integer('prom_update_by')->nullable();
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
        Schema::dropIfExists('employee_promotions');
    }
}
