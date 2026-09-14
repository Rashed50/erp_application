<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_transaction', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('tr_no')->nullable();
            $table->dateTime('date');
            $table->float('total_amount',11,2);
            $table->string('general_particular')->nullable();
            $table->unsignedBigInteger('sales_id')->nullable();
            $table->unsignedBigInteger('approve_by')->nullable();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_transaction');
    }
};
