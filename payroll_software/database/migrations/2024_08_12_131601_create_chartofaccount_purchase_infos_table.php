<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartofaccountPurchaseInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartofaccount_purchase_infos', function (Blueprint $table) {
            $table->bigIncrements('ca_purch_auto_id');
            $table->integer('cpi_debit_account_id');
            $table->integer('cpi_credit_account_id');
            $table->integer('cpi_journal_id');
            $table->string('supplyer_invoice_no')->uniqe();
            $table->integer('supplyer_auto_id');
         //   $table->foreign('supplyer_auto_id')->references('cus_auto_id')->on('chartofacc_sales_customers');
            $table->string('cpi_invoice_no');
            $table->string('cpi_inv_remarks')->nullable();
            $table->date('cpi_purchase_date');
            $table->string('cpi_payment_terms');
            $table->float('cpi_total_amount',11,2)->default(0);
            $table->float('cpi_discount_amount',11,2)->default(0);
            $table->float('cpi_vat_amount',11,2)->default(0);
            $table->float('cpi_grand_total_amount',11,2)->default(0);
            $table->boolean('cpi_payment_status')->default(0);
            $table->integer('created_by_id');
            $table->integer('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    // debit account id
    // credit account id
    // journal account id
    // payment status
    // invoice status (draft, posted,)

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartofaccount_purchase_infos');
    }
}
