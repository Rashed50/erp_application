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
        // A snapshot of every generated monthly salary. All values used in the
        // calculation are copied here so later salary or employee changes never
        // alter history.
        Schema::create('salary_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('salary_detail_id')->nullable()->constrained('salary_details')->nullOnDelete();
            $table->foreignId('emp_work_id')->nullable()->constrained('emp_works')->nullOnDelete();
            $table->date('salary_month')->index();

            // Employee snapshot.
            $table->string('employee_code', 50);
            $table->string('employee_name');
            $table->string('department', 100)->nullable()->index();
            $table->string('designation', 100)->nullable();

            // Work snapshot.
            $table->unsignedSmallInteger('days_in_month');
            $table->unsignedSmallInteger('employed_days');
            $table->decimal('working_days', 5, 2)->default(0);
            $table->decimal('present_days', 5, 2)->default(0);
            $table->decimal('absent_days', 5, 2)->default(0);
            $table->decimal('paid_leave_days', 5, 2)->default(0);
            $table->decimal('unpaid_leave_days', 5, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_rate', 15, 2)->default(0);

            // Earnings.
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('house_rent', 15, 2)->default(0);
            $table->decimal('medical_allowance', 15, 2)->default(0);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('food_allowance', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('other_addition', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2)->default(0);

            // Deductions.
            $table->string('deduction_basis', 10)->default('basic');
            $table->decimal('per_day_rate', 15, 2)->default(0);
            $table->decimal('absence_deduction', 15, 2)->default(0);
            $table->decimal('unpaid_leave_deduction', 15, 2)->default(0);
            $table->decimal('other_deduction', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);

            $table->string('status', 20)->default('Generated')->index();
            // 1 while the record is live, NULL once cancelled. The unique index
            // below then allows at most one live salary per employee and month,
            // while keeping cancelled records for audit.
            $table->boolean('is_active_record')->nullable()->default(true);
            $table->text('remarks')->nullable();

            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'salary_month', 'is_active_record'], 'salary_history_live_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_history');
    }
};
