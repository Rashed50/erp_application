<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_buildings', function (Blueprint $table) {
            $table->id('ofb_id');
            $table->string('ofb_name');   // Column added without run migration
            $table->date('ofb_rent_date');
            $table->string('ofb_rent_form')->nullable();
            $table->string('ofb_owner_mobile');
            $table->double('ofb_rent_amount')->comment('Per Month');
            $table->double('ofb_advance_amount');
            $table->date('ofb_agrement_date');
            $table->date('ofb_expiration_date');
            $table->string('ofb_dead_papers')->nullable();
            $table->string('ofb_city_name');
            $table->string('ofb_loct_details')->nullable();
            $table->integer('ofb_accomod_capacity');
            $table->integer('ofb_manage_by_emp_auto_id');
            $table->enum('ofb_agrement_type', ["new", "renewal"])->default("new");
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
        Schema::dropIfExists('office_buildings');
    }
}
