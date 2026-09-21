<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Parent (header) tables that carry the approval and soft-delete audit
     * trail. Child rows (items, ledger transactions) are excluded.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'customers',
        'suppliers',
        'purchases',
        'sales',
        'income_expense_accounts',
        'income_expense_transactions',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }

                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();

                if (! Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }

                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropConstrainedForeignId('deleted_by');
                $table->dropConstrainedForeignId('approved_by');
                $table->dropColumn('approved_at');

                if (in_array($tableName, ['customers', 'suppliers', 'income_expense_accounts', 'income_expense_transactions'], true)) {
                    $table->dropSoftDeletes();
                }

                if ($tableName === 'income_expense_accounts') {
                    $table->dropConstrainedForeignId('updated_by');
                }
            });
        }
    }
};
