<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id('district_id');
            $table->string('district_name');
            $table->integer('country_id')->default(1);
            $table->integer('division_id');
            $table->timestamps();
        });
        //
        DB::table('districts')->insert(
            array(
                  'district_name' => 'Dhaka Sadar',
                  'division_id' => '1'  // dhaka division
            )
          );
        DB::table('districts')->insert(
          array(
                'district_name' => 'Gazipur',
                'division_id' => '1'
          )
        );
        DB::table('districts')->insert(
            array(
                  'district_name' => 'Meherpur',
                  'division_id' => '2' // Khulna Division
            )
          );
        
        DB::table('districts')->insert(
          array(
                'district_name' => 'Chuadanga',
                'division_id' => '2'  
          )
        );
        
        DB::table('districts')->insert(
          array(
                'district_name' => 'Jashore',
                'division_id' => '2'
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
        Schema::dropIfExists('districts');
    }
}
