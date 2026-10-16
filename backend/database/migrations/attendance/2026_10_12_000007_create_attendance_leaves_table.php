<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_leaves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_auto_id');
            $table->date('leave_date');
            $table->enum('leave_type', ['annual', 'sick', 'casual', 'unpaid', 'other'])->default('other');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('day_fraction', 3, 2)->default(1.00);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('approved_by_employee_auto_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->foreign('employee_auto_id', 'attendance_leaves_employee_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('approved_by_employee_auto_id', 'attendance_leaves_approver_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->unique(['employee_auto_id', 'leave_date'], 'attendance_leaves_employee_date_unique');
            $table->index(['leave_date', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_leaves'); }
};
