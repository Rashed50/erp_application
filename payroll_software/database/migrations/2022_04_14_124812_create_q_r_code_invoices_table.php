<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQRCodeInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_recordss', function (Blueprint $table) {
            $table->id('invoice_record_auto_id');
            $table->integer('income_sources_id');
            $table->string('invoice_no');
            $table->string('main_contractor_en')->nullable();
            $table->string('main_contractor_arb')->nullable();
            $table->string('main_con_vat_no')->nullable();
            $table->string('sub_contractor_en');
            $table->string('sub_contractor_arb');
            $table->string('sub_con_vat_no');
            $table->double('percent_of_vat')->default(0);
            $table->double('percent_of_retention')->default(0);
            $table->double('total_amount');
            $table->text('remarks');
            $table->integer('project_id')->default(0);
            $table->date('submitted_date');
            $table->integer('entered_by_id');
            $table->integer('invoice_status_id')->default(0);
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('q_r_code_invoices');
    }
}
