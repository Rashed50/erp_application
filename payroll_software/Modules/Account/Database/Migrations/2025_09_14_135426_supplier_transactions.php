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
        Schema::create('suplier_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('suptran_id')->autoIncrement();
            $table->unsignedBigInteger('transaction_id')->nullable()->comment('Transaction No');
            $table->unsignedBigInteger('supplier_id')->comment('Supplier table suplier_id');
            $table->string('transaction_type')->comment('like: Bill, Payment etc')->nullable();
            $table->string('invoice_no')->comment('like: purchase,payment invoice no')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->boolean('st_status')->default(1);
            $table->text('notes')->nullable()->comment('Additional notes or remarks');
            $table->integer('created_by')->comment('Created User Id');
            $table->integer('updated_by')->nullable()->comment('Updated User Id');
            $table->unsignedSmallInteger('branch_office_id')->default(1); //
            $table->foreign('supplier_id')->references('supplier_id')->on('suplier_subsidiary_ledger')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('account_transaction')->onDelete('cascade');
            $table->timestamps();

             $table->index('supplier_id');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
