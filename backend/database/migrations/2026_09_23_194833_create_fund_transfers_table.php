<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An internal fund transfer between two cash/bank accounts, ported from
     * the payroll_software Account module. Each row is a double entry: the
     * sender (credit) account is credited with the total, the receiver
     * (debit) account is debited with the amount, and Bank Charges is debited
     * with the bank charge plus VAT.
     */
    public function up(): void
    {
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->foreignId('debit_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->string('receipt_no')->nullable();
            $table->date('transfer_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('bank_charge', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('branch_office_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
