<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->default('default');
            $table->decimal('standard_work_hours', 5, 2)->default(8.00);
            $table->decimal('overtime_threshold_hours', 5, 2)->default(8.00);
            $table->unsignedSmallInteger('default_break_minutes')->default(60);
            $table->time('default_shift_start')->default('09:00:00');
            $table->time('default_shift_end')->default('18:00:00');
            $table->unsignedSmallInteger('late_grace_minutes')->default(0);
            $table->unsignedSmallInteger('early_leave_grace_minutes')->default(0);
            $table->boolean('deduct_break_from_worked_hours')->default(true);
            $table->boolean('overtime_requires_approval')->default(true);
            $table->boolean('allow_manual_attendance')->default(true);
            $table->boolean('allow_manual_override_of_device_logs')->default(false);
            $table->string('default_daily_status', 30)->default('present');
            $table->json('rules')->nullable();
            $table->timestamps();
            $table->unique('name');
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_settings'); }
};
