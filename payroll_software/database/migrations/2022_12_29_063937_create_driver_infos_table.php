<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_infos', function (Blueprint $table) {
            $table->id('dri_auto_id');
            $table->unsignedBigInteger('dri_emp_id');
            $table->integer('dri_emp_type')->default(1);
            $table->integer('dri_license_type_id')->default(1);
            $table->string('dri_name', 50)->nullable();
            $table->string('dri_address')->nullable();
            $table->string('dri_iqama_no',50)->nullable();
            $table->string('dri_iqama_certificate',100)->nullable();
            $table->string('dri_license_certificate',100);
            $table->string('dri_ins_certificate',100);
            $table->integer('insert_by');
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
        Schema::dropIfExists('driver_infos');
    }
}
