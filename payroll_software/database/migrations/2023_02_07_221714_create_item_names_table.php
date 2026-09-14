<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_names', function (Blueprint $table) {
            $table->id('item_id');
            $table->integer('itype_id');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            $table->string('item_deta_name',50);
            $table->string('item_deta_code',10);
            $table->boolean('item_deta_status')->default(1);
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
        Schema::dropIfExists('item_names');
    }
}
