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
        Schema::create('item_names', function (Blueprint $table) {
            $table->id('item_id');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            // 1 = Asset, 2 = Non-Asset (see ItemName::TYPES).
            $table->integer('itype_id');
            $table->string('item_name', 50);
            $table->string('item_title', 50);
            $table->string('item_code', 10);
            $table->boolean('item_status')->default(1);
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();

            $table->foreignId('unit_id')->nullable()->after('itype_id')->constrained('inventory_units')->nullOnDelete();
            $table->string('track_type', 20)->default('quantity')->after('unit_id');
            $table->decimal('minimum_stock', 15, 3)->default(0)->after('track_type');

            $table->timestamps();
        });

          // 1 = Asset (see ItemName::TYPE_ASSET).
      // DB::table('item_names')->where('itype_id', 1)->update(['track_type' => 'serial']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_names');
    }
};
