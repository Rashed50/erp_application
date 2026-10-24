<?php

use App\Models\AccountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Advance salary and salary payments now post journal entries: an advance
     * is money out of a cash/bank account into "Advance to Employees", cash
     * paid back moves it the other way, and paying a salary debits Salaries
     * & Wages against the cash/bank account and the recovered installments.
     * Each record keeps the cash/bank account it moved money through.
     */
    public function up(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('employee_id')->constrained('chart_of_accounts')->restrictOnDelete();
        });

        Schema::table('employee_advance_recoveries', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('salary_history_id')->constrained('chart_of_accounts')->restrictOnDelete();
        });

        Schema::table('salary_history', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('paid_at')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->date('payment_date')->nullable()->after('payment_account_id');
        });

        $this->addAdvanceToEmployeesAccount();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_history', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropColumn('payment_date');
        });

        Schema::table('employee_advance_recoveries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });

        Schema::table('employee_advances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
        });
    }

    /**
     * Databases seeded before this account existed get it here, under the
     * Assets group, the same way ChartOfAccountSeeder creates it.
     */
    private function addAdvanceToEmployeesAccount(): void
    {
        $assets = DB::table('chart_of_accounts')->where('account_number', '1000')->first();

        if (! $assets || DB::table('chart_of_accounts')->where('account_number', '1080')->exists()) {
            return;
        }

        DB::table('chart_of_accounts')->insert([
            'account_type_id' => AccountType::ASSET,
            'parent_id' => $assets->id,
            'name' => 'Advance to Employees',
            'account_number' => '1080',
            'sibling_level' => $assets->sibling_level + 1,
            'opening_date' => today(),
            'is_transaction' => true,
            'is_predefined' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
