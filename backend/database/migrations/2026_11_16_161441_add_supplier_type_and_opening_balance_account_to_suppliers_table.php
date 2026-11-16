<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A supplier provides goods or services. `opening_balance_account_id` is
     * the account debited when the opening balance is credited to Accounts
     * Payable (e.g. an expense account for a service supplier); null keeps
     * the earlier behaviour of debiting the Assets group account.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_type', 20)->default('goods')->after('name');
            $table->foreignId('opening_balance_account_id')->nullable()->after('opening_balance')->constrained('chart_of_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opening_balance_account_id');
            $table->dropColumn('supplier_type');
        });
    }
};
