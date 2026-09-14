<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_credits', function (Blueprint $table) {
            $table->id('DebiCredId');
            $table->float('Amount',11,2);
            $table->unsignedBigInteger('TranId');
            $table->unsignedBigInteger('ChartOfAcctId');
            $table->unsignedBigInteger('DrCrTypeId'); // 1 debit , 2 = credit
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debit_credits');
    }
}
