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
        // One monthly work record per employee; salary_month is always the
        // first day of the month.
        Schema::create('emp_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->date('salary_month')->index();
            $table->decimal('working_days', 5, 2)->default(0);
            $table->decimal('present_days', 5, 2)->default(0);
            $table->decimal('absent_days', 5, 2)->default(0);
            $table->decimal('paid_leave_days', 5, 2)->default(0);
            $table->decimal('unpaid_leave_days', 5, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('other_addition', 15, 2)->default(0);
            $table->decimal('other_deduction', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'salary_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_works');
    }
};
