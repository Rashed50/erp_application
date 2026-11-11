<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A scanned/uploaded copy of the invoice or voucher, stored on the public disk.
        foreach (['sales', 'purchases', 'income_expense_transactions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('attachment')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['sales', 'purchases', 'income_expense_transactions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }
    }
};
