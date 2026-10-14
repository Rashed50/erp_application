<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number', 100)->unique();
            $table->string('name', 150)->nullable();
            $table->string('model', 100)->nullable()->default('ZKTeco F35');
            $table->string('location', 255)->nullable();
            $table->string('timezone', 64)->default('Asia/Dhaka');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'serial_number']);
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_devices'); }
};
