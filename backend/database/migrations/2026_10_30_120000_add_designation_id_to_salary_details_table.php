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
            // The designation given with this revision (promotion or demotion),
            // kept so the designation history stays with the salary history.
            $table->foreignId('designation_id')->nullable()->after('change_reason')->constrained('designations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('designation_id');
        });
    }
};
