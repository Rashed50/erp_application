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
        Schema::create('upload_advance_papers', function (Blueprint $table) {
          $table->id('uap_auto_id');
          $table->integer('month');
          $table->integer('year');
          $table->date('advance_date');
          $table->boolean('status')->default(1); // 1 active , 0 inactive
          $table->string('file_path')->nullable();
          $table->string('remark')->nullable();
          $table->integer('insert_by');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_advance_papers');
    }
};
