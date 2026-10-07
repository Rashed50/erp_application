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
        // Advance salary given to an employee, recovered by a monthly
        // installment deducted at salary generation (or repaid in cash), as in
        // the payroll_software advance module.
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->date('advance_date');
            $table->decimal('amount', 15, 2);
            $table->unsignedSmallInteger('installment_count');
            $table->decimal('installment_amount', 15, 2);
            // First day of the first month the installment is deducted from salary.
            $table->date('deduction_start_month')->index();
            $table->string('purpose', 150)->nullable();
            $table->text('remarks')->nullable();
            // Kept in sync with the recoveries below for quick listing.
            $table->decimal('recovered_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('Running')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Every repayment of an advance: an installment deducted from a
        // generated salary, or cash paid back by the employee.
        Schema::create('employee_advance_recoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_advance_id')->constrained('employee_advances')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('salary_history_id')->nullable()->constrained('salary_history')->cascadeOnDelete();
            $table->string('type', 10)->index();
            $table->date('recovery_date');
            $table->date('salary_month')->nullable()->index();
            $table->decimal('amount', 15, 2);
            $table->string('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_advance_recoveries');
        Schema::dropIfExists('employee_advances');
    }
};
