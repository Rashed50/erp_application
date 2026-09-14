<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_servicing_names', function (Blueprint $table) {
            $table->unsignedBigInteger('ser_nam_auto_id', true);
            $table->string('service_name')->nullable();
            $table->boolean('ser_nam_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table');

    }
};
