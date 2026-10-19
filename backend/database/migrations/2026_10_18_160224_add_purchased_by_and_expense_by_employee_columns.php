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
        // The employee who made the purchase, separate from the user who entered it.
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('purchased_by_id')->nullable()->after('work_order_id')->constrained('employee_info')->nullOnDelete();
        });

        // The employee who spent (expense) or received (income) the money.
        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('work_order_id')->constrained('employee_info')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchased_by_id');
        });
    }
};
