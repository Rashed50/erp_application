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
    {
        Schema::create('bd_office_payments', function (Blueprint $table) {
            $table->id('bdofpay_auto_id');
            $table->integer('emp_auto_id');
            $table->float('approved_amount',11,2);
			$table->float('approved_amount_tk',11,2);
            $table->float('paid_amount',11,2)->default(0);
            $table->integer('insert_by');
            $table->integer('approved_by')->nullable();
            $table->integer('update_by')->nullable();            
            $table->date('approved_date')->nullable();          
            $table->integer('paid_status')->default(0); // 0 = unpaid , 1= paid
            $table->integer('status')->default(1); // 0 = inactive , 1= active
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
        Schema::dropIfExists('bd_office_payments');
    }
};
