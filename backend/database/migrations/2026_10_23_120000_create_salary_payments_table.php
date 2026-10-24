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
     * A salary can now be paid in several payments. Approving it books the
     * expense against Salary Payable, and each payment clears part of that
     * from a cash/bank account. The salary keeps the paid total so its due
     * amount (net salary − paid) is quick to list.
     */
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_history_id')->constrained('salary_history')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->foreignId('payment_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->date('payment_date')->index();
            $table->decimal('amount', 15, 2);
            $table->string('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('salary_history', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('net_salary');
        });

        // Salaries paid before payments were recorded one by one were paid in full.
        DB::table('salary_history')->where('status', 'Paid')->update(['paid_amount' => DB::raw('net_salary')]);

        // The account and date now live on each payment.
        if (Schema::hasColumn('salary_history', 'payment_account_id')) {
            Schema::table('salary_history', function (Blueprint $table) {
                $table->dropConstrainedForeignId('payment_account_id');
                $table->dropColumn('payment_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_history', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->foreignId('payment_account_id')->nullable()->after('paid_at')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->date('payment_date')->nullable()->after('payment_account_id');
        });

        Schema::dropIfExists('salary_payments');
    }
};
