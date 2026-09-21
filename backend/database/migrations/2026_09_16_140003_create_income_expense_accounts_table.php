<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A minimal, self-contained "chart of accounts" for the income/expense
     * double-entry module — not the full multi-level chart of accounts the
     * source payroll_software module used (App\Models\AccountsModule\ChartOfAccounts),
     * which does not exist in this application.
     */
    public function up(): void
    {
        Schema::create('income_expense_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // 'asset' accounts (e.g. Cash, Bank) are the payment side of an
            // entry; 'income' and 'expense' accounts are the category side.
            $table->enum('type', ['asset', 'income', 'expense']);
            $table->boolean('active_status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_expense_accounts');
    }
};
