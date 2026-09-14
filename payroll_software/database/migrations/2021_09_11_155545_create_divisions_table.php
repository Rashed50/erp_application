<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divisions', function (Blueprint $table) {
          $table->id('division_id');
          $table->string('division_name');
          $table->integer('country_id');
          $table->timestamps();
        });

        DB::table('divisions')->insert(
          array(
                'division_name' => 'Dhaka',
                'country_id' => '1'

          )
        );



        DB::table('divisions')->insert(
          array(
                'division_name' => 'Khulna',
                'country_id' => '1'

          )
        );


        DB::table('divisions')->insert(
          array(
                'division_name' => 'Rajshahi',
                'country_id' => '1'
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
        Schema::dropIfExists('divisions');
    }
}
