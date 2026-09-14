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
        Schema::create('customer_transactions', function (Blueprint $table) {
 
            $table->unsignedBigInteger('custran_id')->autoIncrement();

            $table->unsignedBigInteger('transaction_id')->nullable()->comment('Transaction No');
            $table->unsignedBigInteger('customer_id')->comment('CustomerSubsidary table customer_id');
            $table->string('transaction_type')->comment('like: Sales, Payment etc')->nullable();
            $table->string('invoice_no')->comment('like: sales,payment invoice no')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->text('notes')->nullable()->comment('Additional notes or remarks');
            $table->integer('created_by')->comment('Created User Id');
            $table->integer('updated_by')->nullable()->comment('Updated User Id');
            $table->unsignedSmallInteger('branch_office_id')->default(1); //
            $table->boolean('ct_status')->default(1);
            // Add index for better performance
            $table->index('customer_id');
            $table->index('transaction_date');

            $table->foreign('customer_id')->references('customer_id')->on('customer_subsidiary_ledger')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('account_transaction')->onDelete('cascade');
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
        Schema::dropIfExists('customer_transactions');
    }
};
