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
        // Salary configuration revisions. A salary change adds a new row with a
        // later effective_date; payroll uses the latest active row effective
        // on or before the end of the salary month.
        Schema::create('salary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->date('effective_date');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('house_rent', 15, 2)->default(0);
            $table->decimal('medical_allowance', 15, 2)->default(0);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('food_allowance', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->decimal('overtime_rate', 15, 2)->default(0);
            // Fixed monthly deduction (e.g. provident fund).
            $table->decimal('other_deduction', 15, 2)->default(0);
            // Whether absence/unpaid-leave day rates are based on basic or on
            // basic + allowances.
            $table->string('deduction_basis', 10)->default('basic');
            $table->boolean('status')->default(true);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_details');
    }
};
