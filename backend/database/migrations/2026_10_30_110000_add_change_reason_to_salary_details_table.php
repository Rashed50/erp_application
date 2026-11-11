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
        Schema::table('salary_details', function (Blueprint $table) {
            // Why the salary changed (Promotion, Demotion, Annual Increment...).
            // Whether it went up or down is worked out from the previous revision.
            $table->string('change_reason', 30)->nullable()->after('deduction_basis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropColumn('change_reason');
        });
    }
};
