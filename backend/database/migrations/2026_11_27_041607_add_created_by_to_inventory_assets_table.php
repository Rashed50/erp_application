<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Who entered an asset (the user receiving the purchase it came from),
     * shown with its entry time on the asset list.
     */
    public function up(): void
    {
        Schema::table('inventory_assets', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
