<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id('deduc_id');
            $table->integer('emp_auto_id')->nullable();
            $table->string('emp_id',50)->nullable();
            $table->integer('emp_catg_id')->nullable();
            $table->integer('desig_id')->nullable();
            $table->integer('depart_id')->nullable();
            $table->float('deduc_amount',11,2)->nullable();
            $table->float('total_installment',11,2)->nullable();
            $table->float('payable_amount',11,2)->nullable();
            $table->string('install_type',50);
            $table->string('deduc_reason',100);
            $table->date('deduc_start_date');
            $table->string('payable_month',20);
            $table->string('deduction_month',20);
            $table->date('entry_date');
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
        Schema::dropIfExists('deductions');
    }
}
