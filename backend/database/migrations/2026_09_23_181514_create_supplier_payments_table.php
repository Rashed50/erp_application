<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A supplier bill payment, ported from the payroll_software Account
     * module's purchase payment. Each row is a double entry: the payment
     * (cash/bank) account is credited with the total, Accounts Payable is
     * debited with the bill amount and Bank Charges with the bank charge.
     */
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            // Optional purchase invoice this payment settles.
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            // The asset (cash/bank) account the money leaves from.
            $table->foreignId('payment_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            // The "Bill Payment" entry this payment posted to the supplier ledger.
            $table->foreignId('supplier_transaction_id')->nullable()->constrained('supplier_transactions')->nullOnDelete();
            $table->string('invoice_no')->nullable();
            $table->date('payment_date');
            $table->decimal('bill_amount', 15, 2);
            $table->decimal('bank_charge', 15, 2)->default(0);
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
        Schema::dropIfExists('supplier_payments');
    }
};
