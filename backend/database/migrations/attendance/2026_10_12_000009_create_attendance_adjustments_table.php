<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daily_attendance_id');
            $table->unsignedBigInteger('requested_by_employee_auto_id')->nullable();
            $table->unsignedBigInteger('reviewed_by_employee_auto_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('requested_values');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->foreign('daily_attendance_id', 'attendance_adjustments_daily_fk')
                ->references('id')->on('daily_attendances')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('requested_by_employee_auto_id', 'attendance_adjustments_requester_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('reviewed_by_employee_auto_id', 'attendance_adjustments_reviewer_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_adjustments'); }
};
