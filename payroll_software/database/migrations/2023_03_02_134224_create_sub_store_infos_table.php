<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubStoreInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_store_infos', function (Blueprint $table) {
            $table->id('sub_store_id');
            $table->string('sub_store_name',30);
            $table->string('sub_store_code',10);
            $table->boolean('sub_store_status')->default(1);
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
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
        Schema::dropIfExists('sub_store_infos');
    }
}
