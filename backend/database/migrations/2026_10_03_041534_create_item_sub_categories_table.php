<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_sub_categories', function (Blueprint $table) {
            $table->id('iscatg_id');
            $table->integer('icatg_id');
            $table->string('iscatg_name', 50);
            $table->string('iscatg_code', 10);
            $table->boolean('status')->default(1);
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sub_categories');
    }
};
