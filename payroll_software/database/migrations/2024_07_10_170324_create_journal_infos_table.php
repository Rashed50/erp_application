<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_infos', function (Blueprint $table) {
            $table->id('jour_id');
            $table->unsignedBigInteger('jour_type_id');
            $table->unsignedBigInteger('chart_of_acct_id');
            $table->string('jour_name');
            $table->string('jour_code', 20)->unique();
            $table->boolean("jour_status")->default(1);
            $table->smallInteger('created_by_id');
            $table->smallInteger('updated_by_id')->nullable();
            $table->timestamps();

            $table->foreign('jour_type_id')->references('jour_type_id')->on('journal_types');
            $table->foreign('chart_of_acct_id')->references('chart_of_acct_id')->on('chart_of_accounts');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_infos');
    }
}
