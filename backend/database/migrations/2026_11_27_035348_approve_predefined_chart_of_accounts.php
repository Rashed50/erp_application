<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An unapproved ledger account can no longer be used, and the automatic
     * postings (sales, purchases, payments, ...) all go to the predefined
     * chart, so the predefined accounts are approved by the system (an
     * approval time without an approver). Accounts users created stay pending
     * until someone approves them.
     */
    public function up(): void
    {
        DB::table('chart_of_accounts')
            ->where('is_predefined', true)
            ->whereNull('approved_at')
            ->update(['approved_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('chart_of_accounts')
            ->where('is_predefined', true)
            ->whereNull('approved_by')
            ->update(['approved_at' => null]);
    }
};
