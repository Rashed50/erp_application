<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->foreignId('payment_account_id')
                ->nullable()
                ->after('credit')
                ->constrained('chart_of_accounts')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });
    }
};
