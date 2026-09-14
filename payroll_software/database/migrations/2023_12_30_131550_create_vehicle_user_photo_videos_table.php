<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleUserPhotoVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_user_photo_videos', function (Blueprint $table) {
            $table->id('vupv_auto_id');
            $table->smallInteger('driv_auto_id');
            $table->smallInteger('veh_auto_id');
            $table->string('video_url')->nullable();  
            $table->smallInteger('inserted_by');
            $table->string('photos')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('vehicle_user_photo_videos');
    }
}
