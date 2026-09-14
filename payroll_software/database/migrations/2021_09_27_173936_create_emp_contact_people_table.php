<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpContactPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_contact_people', function (Blueprint $table) {
            $table->id('ecp_id');
            $table->integer('emp_id');
            $table->string('ecp_name',50);
            $table->string('ecp_mobile1',20);
            $table->string('ecp_mobile2',20)->nullable();
            $table->string('ecp_email',50)->nullable();
            $table->string('ecp_relationship');
            $table->string('details');
            $table->boolean('status')->default(1);
            $table->integer('create_by_id');
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
        Schema::dropIfExists('emp_contact_people');
    }
}
