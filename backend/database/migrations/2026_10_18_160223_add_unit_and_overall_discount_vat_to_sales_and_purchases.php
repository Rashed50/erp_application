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
        // The unit is a snapshot of the name (like `item_name`), so renaming a
        // unit later never changes an old invoice.
        foreach (['sale_items', 'purchase_items'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('unit', 50)->nullable()->after('qty');
            });
        }

        // Invoice-level discount and VAT on top of the per-line amounts; the
        // `discount_amount` / `vat_amount` totals include them.
        foreach (['sales', 'purchases'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('overall_discount', 15, 2)->default(0)->after('vat_amount');
                $table->decimal('overall_vat', 15, 2)->default(0)->after('overall_discount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['sales', 'purchases'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['overall_discount', 'overall_vat']);
            });
        }

        foreach (['sale_items', 'purchase_items'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('unit');
            });
        }
    }
};
