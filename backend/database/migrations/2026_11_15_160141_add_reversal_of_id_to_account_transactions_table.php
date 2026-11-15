<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A reversal entry mirrors an earlier entry's lines (debits become
     * credits) so the original stays in the books as history while its
     * effect on balances is cancelled. `reversal_of_id` links the reversal
     * to the entry it cancels, so an entry is never reversed twice.
     */
    public function up(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->foreignId('reversal_of_id')->nullable()->after('source_id')->constrained('account_transactions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reversal_of_id');
        });
    }
};
