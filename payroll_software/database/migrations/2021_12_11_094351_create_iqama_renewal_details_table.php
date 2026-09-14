<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIqamaRenewalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ALTER TABLE `iqama_renewal_details` ADD `payment_purpose_id` INT NOT NULL DEFAULT '1' AFTER `renewal_status`;

        Schema::create('iqama_renewal_details', function (Blueprint $table) {
            $table->id('IqamaRenewId');
            $table->integer('EmplId');
            $table->float('jawazat_fee', 11, 2)->default(0);
            $table->float('maktab_alamal_fee', 11, 2)->default(0);
            $table->float('bd_amount', 11, 2)->default(0);
            $table->float('medical_insurance', 11, 2)->default(0);
            $table->float('others_fee', 11, 2)->default(0);
            // $table->float('Year', 11, 2)->default(0);
            $table->double('Year', 11, 2)->default('0');
            $table->float('Cost7', 11, 2)->default(0);
            $table->float('Cost8', 11, 2)->default(0);
            // $table->integer('Year')->nullable();
            $table->float('jawazat_penalty', 11, 2)->default(0);
            $table->integer('duration')->default(3);
            $table->date('renewal_date')->nullable();
            $table->string('remarks')->nullable();
            $table->float('total_amount', 11, 2)->default(0);
            $table->string('payment_number')->default();
            $table->date('payment_date')->nullable();
            $table->integer('reference_emp_id')->nullable();
            $table->integer('renewal_status')->default(1);
            $table->integer('expense_paid_by')->default(1);
            $table->date('iqama_expire_date')->nullable();
            $table->integer('inserted_by'); 
            $table->integer('update_by')->nullable();  
            $table->integer('payment_purpose_id')->default(1);
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
        Schema::dropIfExists('iqama_renewal_details');
    }
}
