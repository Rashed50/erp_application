<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replaces the flat `income_expense_accounts` table with the hierarchical
     * chart of accounts used by the source payroll_software Account module
     * (`chart_of_accounts` + `chartof_account_types`). Existing accounts are
     * carried over with their ids, so income/expense entries stay linked.
     */
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Which side of a double entry increases the account's balance.
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->timestamps();
        });

        $now = now();
        DB::table('account_types')->insert([
            ['id' => 1, 'name' => 'Asset', 'normal_balance' => 'debit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Liability', 'normal_balance' => 'credit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Owner Equity', 'normal_balance' => 'credit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Revenue', 'normal_balance' => 'credit', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Expense', 'normal_balance' => 'debit', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_type_id')->constrained('account_types')->restrictOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->restrictOnDelete();
            $table->string('name');
            $table->string('account_number')->nullable()->unique();
            // 0 = parent, 1 = child, 2 = grand child and so on.
            $table->unsignedTinyInteger('sibling_level')->default(0);
            // Signed by the account type's normal balance: a positive value
            // always means the balance sits on the account's normal side.
            $table->decimal('balance', 15, 2)->default(0);
            $table->date('opening_date');
            $table->boolean('active_status')->default(true);
            // Only transaction accounts can have entries posted; the others
            // are group headers.
            $table->boolean('is_transaction')->default(false);
            $table->boolean('is_predefined')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        $typeMap = ['asset' => 1, 'income' => 4, 'expense' => 5];

        DB::table('income_expense_accounts')->orderBy('id')->each(function ($account) use ($typeMap) {
            DB::table('chart_of_accounts')->insert([
                'id' => $account->id,
                'account_type_id' => $typeMap[$account->type],
                'name' => $account->name,
                'opening_date' => substr((string) ($account->created_at ?? now()), 0, 10),
                'active_status' => $account->active_status,
                'is_transaction' => true,
                'created_by' => $account->created_by,
                'updated_by' => $account->updated_by,
                'approved_by' => $account->approved_by,
                'approved_at' => $account->approved_at,
                'deleted_by' => $account->deleted_by,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'deleted_at' => $account->deleted_at,
            ]);
        });

        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->dropForeign(['income_expense_account_id']);
            $table->dropForeign(['payment_account_id']);
        });

        Schema::table('income_expense_transactions', function (Blueprint $table) {
            $table->foreign('income_expense_account_id')->references('id')->on('chart_of_accounts')->restrictOnDelete();
            $table->foreign('payment_account_id')->references('id')->on('chart_of_accounts')->restrictOnDelete();
        });

        Schema::drop('income_expense_accounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('Merging the chart of accounts back into flat income/expense accounts would lose the hierarchy and balances.');
    }
};
