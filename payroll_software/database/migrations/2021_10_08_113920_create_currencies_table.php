<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id('curc_id');
            $table->string('curc_code',10);
            $table->string('curc_name',20);
           // $table->timestamps();
        });

        DB::table('currencies')->insert(
            array(
                  'curc_code' => 'TK',
                  'curc_name' => 'Taka' 
            )
          );

          DB::table('currencies')->insert(
            array(
                  'curc_code' => 'USD',
                  'curc_name' => 'USD' 
            )
          );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
