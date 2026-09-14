<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_infos', function (Blueprint $table) {
            $table->id('ban_id');
            $table->string('ban_title',100);
            $table->string('ban_subtitle',150)->nullable();
            $table->text('ban_description')->nullable();
            $table->string('ban_caption',100)->nullable();
            $table->string('ban_image',100);
            $table->integer('company_id');
            $table->integer('entered_id');
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
        Schema::dropIfExists('banner_infos');
    }
}
