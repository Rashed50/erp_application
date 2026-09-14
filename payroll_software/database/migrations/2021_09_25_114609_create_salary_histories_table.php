<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_histories', function (Blueprint $table) {
            $table->id('slh_auto_id');
            $table->integer('emp_auto_id');

            $table->float('basic_amount',11,2)->default(0);
            $table->integer('basic_hours')->default();
            $table->float('house_rent',11,2)->default(0);
            $table->float('hourly_rent',11,2)->default(0);
            $table->float('mobile_allowance',11,2)->default(0);
            $table->float('medical_allowance',11,2)->default(0);
            $table->float('local_travel_allowance',11,2)->default(0);
            $table->float('conveyance_allowance',11,2)->default(0);
            $table->integer('increment_no')->default(0);
            $table->float('increment_amount',11,2)->default(0);
            $table->float('food_allowance',11,2)->default(0);
            $table->float('others',11,2)->default(0);

            $table->float('slh_total_overtime',4,2)->default(0);
            $table->float('slh_overtime_amount',11,2)->default(0);

            $table->double('slh_total_salary',10,2);
            $table->float('slh_total_hours',4,2);
            $table->integer('slh_total_working_days');
            $table->integer('slh_month');
            $table->integer('slh_year');

            $table->float('slh_cpf_contribution')->default(0);
            $table->float('slh_saudi_tax',11,2)->default(300);
            $table->float('slh_company_contribution')->default(0);
            $table->float('slh_iqama_advance')->default(0);
            $table->float('slh_other_advance')->default(0);
            $table->float('slh_bonus_amount')->default(0);
            $table->float('slh_all_include_amount')->default(0);

            $table->date('slh_salary_date');
            $table->boolean('Status')->default(0);
            $table->boolean('multProject')->default(0);
            $table->integer('updated_by')->nullable();
            $table->integer('slh_paid_method')->nullable();
            // ALTER TABLE `salary_histories` ADD `slh_paid_method` INT(6) NULL AFTER `slh_all_include_amount`;
            $table->integer('food_deduction')->default(0);
           // ALTER TABLE `salary_histories` ADD `food_deduction` INT(5) NOT NULL DEFAULT '0' AFTER `slh_paid_method`;

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
        Schema::dropIfExists('salary_histories');
    }
}
