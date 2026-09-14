<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_summaries', function (Blueprint $table) {
          $table->id('inv_sum_auto_id');
          $table->integer('project_id');
          $table->integer('month');
          $table->integer('year');
          $table->float('invoice_amount',11,2);
          $table->float('vat_amount',11,2);
          $table->float('retention_amount',11,2)->defalut(0);
          $table->float('receivable_amount',11,2);
          $table->float('vat',3,2);
          $table->float('retention',3,2)->defalut(0);
          $table->date('invoice_date');
          $table->date('submit_date');
          $table->date('payment_received_date')->nullable();
          $table->smallInteger('invoice_status')->default(0);
          $table->string('remarks')->nullable();
          $table->string('invoice_file')->nullable();
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
        Schema::dropIfExists('invoice_summaries');
    }
}
