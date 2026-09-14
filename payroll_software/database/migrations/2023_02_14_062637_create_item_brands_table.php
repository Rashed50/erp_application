<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_brands', function (Blueprint $table) {
            $table->id('ibrand_id');
            $table->integer('itype_id');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            $table->integer('item_id'); // Item Names Table
            $table->string('item_brand_name',50);
            $table->string('item_brand_code',10);
            $table->boolean('item_brand_status')->default(1);
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
        Schema::dropIfExists('item_brands');
    }
}
