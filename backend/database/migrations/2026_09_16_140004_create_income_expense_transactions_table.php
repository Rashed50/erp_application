<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each row is a full double-entry: for an 'income' entry, the payment
     * account is debited (an asset increases) and the income account is
     * credited; for an 'expense' entry, the expense account is debited and
     * the payment account is credited (an asset decreases).
     */
    public function up(): void
    {
        Schema::create('income_expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']);
            // The income/expense category side of the entry.
            $table->foreignId('income_expense_account_id')->constrained('income_expense_accounts')->restrictOnDelete();
            // The asset (cash/bank) side of the entry.
            $table->foreignId('payment_account_id')->constrained('income_expense_accounts')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('reference_no')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('branch_office_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_expense_transactions');
    }
};
