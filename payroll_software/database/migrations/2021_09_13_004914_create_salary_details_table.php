<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_details', function (Blueprint $table) {
            $table->id('sdetails_id');
            $table->integer('emp_id');
            $table->float('basic_amount',11,2)->default(0);
            $table->integer('basic_hours')->nullable();
            $table->float('house_rent',11,2)->default(0);
            $table->float('hourly_rent',11,2)->default(0);
            $table->integer('mobile_allowance')->default(0);
            $table->integer('medical_allowance')->default(0);
            $table->integer('local_travel_allowance')->default(0);
            $table->integer('conveyance_allowance')->default(0);
            $table->integer('increment_no')->default(0);
            $table->float('increment_amount',11,2)->default(0);
            $table->float('others1',11,2)->default(0);
            $table->float('food_allowance',11,2)->default(0);
            $table->float('cpf_contribution',11,2)->default(0);
            $table->float('iqama_adv_inst_amount',11,2)->default(0);
            $table->float('other_adv_inst_amount',11,2)->default(0);
            $table->float('saudi_tax',11,2)->default(300);
            $table->float('others4',11,2)->default(0);
            $table->integer('update_by')->default(1);
            $table->string('payment_method')->default("Cash");
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
        Schema::dropIfExists('salary_details');
    }
}
