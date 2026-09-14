<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
        });


        DB::table('countries')->insert(
          array(
                'country_name' => 'Bangladesh' 
          )
        );
        DB::table('countries')->insert(
            array(
                  'country_name' => 'Saudi Arabia' 
            )
          );
        DB::table('countries')->insert(
            array(
                  'country_name' => 'India' 
            )
          );
          DB::table('countries')->insert(
            array(
                  'country_name' => 'Pakistan' 
            )
          );

          DB::table('countries')->insert(
            array(
                  'country_name' => 'Nepal' 
            )
          );

          DB::table('countries')->insert(
            array(
                  'country_name' => 'Philippines' 
            )
          );
        
          DB::table('countries')->insert(
            array(
                  'country_name' => 'Egypt' 
            )
          );

          DB::table('countries')->insert(
            array(
                  'country_name' => 'Sudan' 
            )
          );

          DB::table('countries')->insert(
            array(
                  'country_name' => 'Turkey' 
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
        Schema::dropIfExists('countries');
    }
}
