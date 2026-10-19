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
        // Who created the user or role, for the "Inserted By" column of the lists.
        // Records created before this column existed stay NULL.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('image')->constrained('users')->nullOnDelete();
        });

        Schema::table(config('permission.table_names.roles', 'roles'), function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('guard_name')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('permission.table_names.roles', 'roles'), function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
