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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('customer_id')->constrained('work_orders')->nullOnDelete();
        });

        // Tags each ledger entry (invoice and payments) with the work order it
        // belongs to, so payments can be tracked per work order.
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->after('customer_id')->constrained('work_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
        });
    }
};
