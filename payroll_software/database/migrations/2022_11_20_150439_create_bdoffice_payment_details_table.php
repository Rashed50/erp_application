<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBdofficePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bdoffice_payment_details', function (Blueprint $table) {
            $table->id('bdoffpaydetails_auto_id');
            $table->integer('emp_auto_id'); 
            $table->integer('bdofpay_auto_id');
            $table->float('sar_paid_amount',11,2);
            $table->float('bdt_paid_amount',11,2);
            $table->float('exchange_rate',11,2);
            $table->integer('insert_by');
            $table->integer('approved_by');
            $table->integer('update_by');
            $table->integer('paid_by_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('receiver_name',50)->nullable();
            $table->string('receiver_address',100)->nullable();
            $table->string('receiver_mobile',12)->nullable();
            $table->string('relation_with_emp_id',20)->nullable();
            $table->date('payment_received_date')->nullable();
            $table->string('payment_slip',200)->nullable();
            $table->integer('payment_status')->default(0);
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
        Schema::dropIfExists('bdoffice_payment_details');
    }
}
