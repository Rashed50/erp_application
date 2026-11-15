<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Customers and suppliers are soft deleted, so a database-wide unique
     * index on email kept a deleted record's address from ever being used
     * again. Uniqueness among live records is enforced by validation
     * (Rule::unique()->withoutTrashed()); a plain index keeps lookups fast.
     *
     * @var array<int, string>
     */
    private array $tables = ['customers', 'suppliers'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['email']);
                $table->index('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['email']);
                $table->unique('email');
            });
        }
    }
};
