<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tags purchases and income/expense entries with the work order they were
     * spent on, so each work order's costs can be reported against its sales.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('supplier_id')->constrained('work_orders')->nullOnDelete();
        });

        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('type')->constrained('work_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
        });
    }
};
