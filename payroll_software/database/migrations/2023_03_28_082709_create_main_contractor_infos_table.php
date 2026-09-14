<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainContractorInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_contractor_infos', function (Blueprint $table) {
            $table->id('mc_auto_id');
            $table->string('en_name');
            $table->string('ar_name');
            $table->string('vat_no');
            $table->string('phone_no',20)->nullable();
            $table->string('mc_email')->nullable();
            $table->string('mc_address',200)->nullable();
            $table->boolean('mc_status')->default(1);
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
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
        Schema::dropIfExists('main_contractor_infos');
    }
}
