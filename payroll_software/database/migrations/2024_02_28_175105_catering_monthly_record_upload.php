<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CateringMonthlyRecordUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_monthly_records_upload', function (Blueprint $table) {
            $table->increments('emcr_auto_id');
            $table->integer('emp_auto_id');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->tinyInteger('total_days');
            $table->smallInteger('amount');
            $table->smallInteger('inserted_by');
            $table->smallInteger('updated_by')->nullable();
            $table->smallInteger('approved_by');
            $table->string('remarks')->nullable();
            $table->boolean('status')->default(1);
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
        //
    }
}
