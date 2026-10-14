<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('attendance_device_id')->constrained('attendance_devices')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->string('pin', 100); // preserve leading zeroes
            $table->dateTime('event_time');
            $table->unsignedInteger('event')->nullable();
            $table->unsignedTinyInteger('inoutstatus')->nullable();
            $table->unsignedInteger('verifytype')->nullable();
            $table->unsignedInteger('eventaddr')->nullable();
            $table->string('cardno', 100)->nullable();
            $table->unsignedBigInteger('event_index')->nullable();
            $table->string('sitecode', 100)->nullable();
            $table->string('linkid', 100)->nullable();
            $table->string('maskflag', 100)->nullable();
            $table->decimal('temperature', 6, 2)->nullable();
            $table->decimal('convtemperature', 6, 2)->nullable();
            $table->longText('raw_data')->nullable();
            $table->string('processing_status', 30)->default('pending');
            $table->text('processing_error')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['attendance_device_id', 'event_time']);
            $table->index(['pin', 'event_time']);
            $table->index(['processing_status', 'received_at']);
            // MySQL allows multiple NULL values in a unique index.
            $table->unique(['attendance_device_id', 'event_index'], 'attendance_logs_device_event_index_unique');
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_logs'); }
};
