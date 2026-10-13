<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The asset item master (item_names) doubles as the inventory item master:
     * asset items are always tracked by serial, non-asset items by quantity
     * unless set otherwise.
     */
    public function up(): void
    {
        Schema::table('item_names', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('itype_id')->constrained('inventory_units')->nullOnDelete();
            $table->string('track_type', 20)->default('quantity')->after('unit_id');
            $table->decimal('minimum_stock', 15, 3)->default(0)->after('track_type');
        });

        // 1 = Asset (see ItemName::TYPE_ASSET).
        DB::table('item_names')->where('itype_id', 1)->update(['track_type' => 'serial']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_names', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn(['track_type', 'minimum_stock']);
        });
    }
};
