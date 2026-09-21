<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Added as a follow-up migration rather than editing
     * 2026_09_16_130002_create_purchases_table, since that migration has
     * already run against the shared dev database.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // How much of net_total has been paid to the supplier so far —
            // net_total minus paid_amount is the outstanding "due" amount.
            $table->decimal('paid_amount', 15, 2)->default(0)->after('net_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
