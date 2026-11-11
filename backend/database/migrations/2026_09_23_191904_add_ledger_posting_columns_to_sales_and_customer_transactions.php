<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `sales.is_ledger_posted` marks whether a sale posted its double entry
     * (debit Accounts Receivable, credit Sales Revenue), so editing or
     * deleting a sale created before automatic posting never reverses an
     * entry that was never made.
     *
     * `customer_transactions.payment_account_id` is the cash/bank account a
     * "Payment Received" entry was debited to, so reversing the entry can
     * reverse that posting too.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('is_ledger_posted')->default(false)->after('paid_amount');
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('credit')->constrained('chart_of_accounts')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('is_ledger_posted');
        });
    }
};
