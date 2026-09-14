<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('daily_costs', function (Blueprint $table) {
            $table->id('cost_id');
            $table->unsignedBigInteger('sub_comp_id');
            $table->integer('cost_type_id');
            $table->integer('project_id');
            $table->integer('employee_id');
            $table->date('voucher_date');
            $table->string('vouchar',100)->nullable();
            $table->string('vouchar_no',50);
            $table->integer('entered_id');
            $table->float('gross_amount',12,2);
            $table->integer('vat')->default(0);
            $table->float('total_amount')->default(0);
            $table->integer('month');
            $table->integer('year');
            $table->integer('approved_by')->nullable();
            $table->string('description', 100)->nullable();
            $table->string('status',30)->default('pending');
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
        Schema::dropIfExists('daily_costs');
    }
}
