<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id('dep_id');
            $table->string('dep_name',100);
            $table->string('dep_code',50)->nullable();
            $table->boolean('dep_status')->default(1);
            $table->timestamps();
        });



        DB::table('departments')->insert(
            array(
                  'dep_name' => 'Admin',
                  'dep_code' => 'A-100'   
            )
          );
          DB::table('departments')->insert(
            array(
                  'dep_name' => 'Finance',
                  'dep_code' => 'F-200'   
            )
          );

          DB::table('departments')->insert(
            array(
                  'dep_name' => 'Engineering',
                  'dep_code' => 'E-300'   
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
        Schema::dropIfExists('departments');
    }
}
