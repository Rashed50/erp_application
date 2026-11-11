<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The date a cash/bank journal line appeared on the bank statement, set
     * from the bank reconciliation report. A line without one (or cleared
     * after the statement date) is still outstanding on that statement.
     */
    public function up(): void
    {
        Schema::table('account_transaction_details', function (Blueprint $table) {
            $table->date('cleared_date')->nullable()->after('particular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_transaction_details', function (Blueprint $table) {
            $table->dropColumn('cleared_date');
        });
    }
};
