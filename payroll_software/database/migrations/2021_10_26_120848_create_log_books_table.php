<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_books', function (Blueprint $table) {
            $table->id('lgb_id');
            $table->integer('veh_id');
            $table->integer('present_miles');
            $table->double('average_miles',20,2)->default(0);
            $table->double('fouel_amount',20,2);
            $table->double('total_cost',20,2);
            $table->date('date');
            $table->string('vouchar_photo',150)->nullable();
            $table->integer('create_by_id');
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
        Schema::dropIfExists('log_books');
    }
}
