<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_auto_id');
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->date('attendance_date');
            $table->unsignedBigInteger('attendance_shift_id')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'leave', 'holiday', 'weekly_off', 'incomplete', 'offsite'])->default('incomplete');
            $table->enum('source', ['manual', 'biometric', 'import', 'system'])->default('system'); // no ui
            $table->dateTime('first_punch_at')->nullable();
            $table->dateTime('last_punch_at')->nullable();
            $table->unsignedSmallInteger('break_minutes')->default(60); // no
            $table->decimal('gross_hours', 7, 2)->default(0);
            $table->decimal('worked_hours', 7, 2)->default(0);
            $table->decimal('standard_work_hours', 5, 2)->default(8.00);
            $table->decimal('overtime_threshold_hours', 5, 2)->default(8.00);
            $table->decimal('overtime_hours', 7, 2)->default(0);
            $table->decimal('approved_overtime_hours', 7, 2)->default(0); // no ui
            $table->decimal('payable_day_fraction', 3, 2)->default(0); // no ui
            $table->boolean('is_late')->default(false); // no ui
            $table->unsignedSmallInteger('late_minutes')->default(0);
            $table->boolean('is_early_leave')->default(false); // no 
            $table->unsignedSmallInteger('early_leave_minutes')->default(0); // no 
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('entered_by_employee_auto_id')->nullable(); // no 
            $table->unsignedBigInteger('approved_by_employee_auto_id')->nullable(); // no 
            $table->timestamp('approved_at')->nullable(); // no
            $table->timestamp('locked_at')->nullable(); // no
            $table->timestamps();
            $table->foreign('employee_auto_id', 'daily_attendances_employee_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('attendance_shift_id', 'daily_attendances_shift_fk')
                ->references('id')->on('attendance_shifts')->cascadeOnUpdate()->nullOnDelete();

            $table->foreign('entered_by_employee_auto_id', 'daily_attendances_entered_by_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();

            $table->foreign('approved_by_employee_auto_id', 'daily_attendances_approved_by_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->unique(['employee_auto_id', 'attendance_date'], 'daily_attendances_employee_date_unique');
            $table->index(['attendance_date', 'status']);
            $table->index(['attendance_date', 'source']);
            $table->index(['locked_at', 'approved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_attendances');
    }
};
