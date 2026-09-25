<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Marks whether a purchase posted its double entry (debit Purchase,
     * credit Accounts Payable) to the chart of accounts, so editing or
     * deleting a purchase created before automatic posting existed never
     * reverses an entry that was never made.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->boolean('is_ledger_posted')->default(false)->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('is_ledger_posted');
        });
    }
};
