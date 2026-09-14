<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_activities_type', function (Blueprint $table) {
            $table->id();
            $table->string('da_type_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_activities_type');
    }
};
