<?php

use App\Services\LedgerPostingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The account a bill payment debits, chosen on the payment form. Every
     * earlier payment debited Accounts Payable, so they are backfilled with it.
     */
    public function up(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->foreignId('debit_account_id')->nullable()->after('payment_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
        });

        $payableId = DB::table('chart_of_accounts')->where('account_number', LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER)->value('id');

        if ($payableId) {
            DB::table('supplier_payments')->whereNull('debit_account_id')->update(['debit_account_id' => $payableId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('debit_account_id');
        });
    }
};
