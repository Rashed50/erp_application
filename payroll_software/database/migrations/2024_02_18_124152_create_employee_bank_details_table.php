<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeBankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_bank_details', function (Blueprint $table) {
            $table->increments('ebd_auto_id');
            $table->integer('emp_auto_id');
            $table->string('acc_holder_name'); 
            $table->string('acc_number'); 
            $table->string('acc_iban'); 
            $table->integer('bank_id');
            $table->boolean('is_active')->default(1);
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('employee_bank_details');
    }
}
