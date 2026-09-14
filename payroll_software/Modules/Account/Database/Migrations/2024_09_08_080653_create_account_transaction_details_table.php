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
        Schema::create('account_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('account_no');
            $table->float('debit', 11, 2)->default(0); 
            $table->float('credit', 11, 2)->default(0); 
            $table->text('particular')->nullable(); 
            
            $table->unsignedBigInteger('trd_id');
            $table->foreign('trd_id')->references('id')->on('account_transaction')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_transaction_details');
    }
};
