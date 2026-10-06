<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The bank columns that move out of emp_details: new name => old name.
     *
     * @var array<string, string>
     */
    private array $columns = [
        'bank_name' => 'bank_name',
        'branch_name' => 'bank_branch',
        'account_name' => 'bank_account_name',
        'account_no' => 'bank_account_no',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // One row per employee: the account salaries are paid into when the
        // payment method (emp_details.payment_method) is Bank.
        Schema::create('emp_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employee_info')->cascadeOnDelete();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no', 50)->nullable();
            $table->string('routing_no', 50)->nullable();
            $table->timestamps();
        });

        DB::table('emp_details')
            ->where(fn ($query) => $query->whereNotNull('bank_name')->orWhereNotNull('bank_account_no'))
            ->orderBy('id')
            ->each(function (object $detail) {
                DB::table('emp_bank_details')->insert([
                    'employee_id' => $detail->employee_id,
                    ...array_map(fn (string $old) => $detail->{$old}, $this->columns),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('emp_details', function (Blueprint $table) {
            $table->dropColumn(array_values($this->columns));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emp_details', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_branch');
            $table->string('bank_account_no', 50)->nullable()->after('bank_account_name');
        });

        DB::table('emp_bank_details')->orderBy('id')->each(function (object $bank) {
            DB::table('emp_details')
                ->where('employee_id', $bank->employee_id)
                ->update(array_combine(
                    array_values($this->columns),
                    array_map(fn (string $new) => $bank->{$new}, array_keys($this->columns)),
                ));
        });

        Schema::dropIfExists('emp_bank_details');
    }
};
