<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Moves the general ledger onto the journal the payroll_software Account
     * module keeps: every posting is an `account_transactions` header with
     * its debit and credit lines in `account_transaction_details`, and an
     * account's balance is always derived from those lines.
     *
     * `chart_of_accounts.balance` therefore stops being a running total and
     * becomes `opening_balance`, the figure entered when the account was
     * created (payroll's `acct_balance`). The `is_ledger_posted` flags are
     * dropped because a posted sale or purchase is now one that owns a
     * journal entry.
     */
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tr_no')->nullable();
            $table->date('date');
            $table->decimal('total_amount', 15, 2);
            $table->string('general_particular')->nullable();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            // The record that posted this entry (a sale, purchase, payment,
            // fund transfer, income/expense entry, opening balance, ...).
            $table->nullableMorphs('source');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('date');
        });

        Schema::create('account_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_transaction_id')->constrained('account_transactions')->cascadeOnDelete();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('particular')->nullable();
            $table->timestamps();
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->renameColumn('balance', 'opening_balance');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('is_ledger_posted');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('debit_account_id')->nullable()->after('paid_amount')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->foreignId('credit_account_id')->nullable()->after('debit_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('is_ledger_posted');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('debit_account_id')->nullable()->after('paid_amount')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->foreignId('credit_account_id')->nullable()->after('debit_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->foreignId('account_transaction_id')->nullable()->after('customer_id')->constrained('account_transactions')->nullOnDelete();
        });

        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->foreignId('account_transaction_id')->nullable()->after('supplier_id')->constrained('account_transactions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_transaction_id');
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_transaction_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_account_id');
            $table->dropConstrainedForeignId('debit_account_id');
            $table->boolean('is_ledger_posted')->default(false)->after('paid_amount');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_account_id');
            $table->dropConstrainedForeignId('debit_account_id');
            $table->boolean('is_ledger_posted')->default(false)->after('paid_amount');
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->renameColumn('opening_balance', 'balance');
        });

        Schema::dropIfExists('account_transaction_details');
        Schema::dropIfExists('account_transactions');
    }
};
