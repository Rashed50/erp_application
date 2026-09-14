<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('income_sources', function (Blueprint $table) {
            $table->id('inc_id');
            $table->string('invoice_no');
            $table->double('total_amount');
            $table->double('vat');
            $table->double('total_with_vat')->comment("total_amount + vat");
            $table->double('debit_amount');
            $table->boolean('invoice_status');
            $table->double('net_amount');
            $table->text('remarks');
            $table->integer('project_id');
            $table->date('submitted_date');
            $table->text('description');
            $table->integer('create_by_id')->nullable();
            $table->integer('submitted_by_id')->nullable()->comment("employee id");
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
        Schema::dropIfExists('income_sources');
    }
}
