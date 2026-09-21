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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            // The ledger entry this sale posted to the customer's subsidiary
            // ledger, mirroring purchases.supplier_transaction_id, so an
            // update/reversal here keeps that entry in sync.
            $table->foreignId('customer_transaction_id')->nullable()->constrained('customer_transactions')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->default(0);
            // How much of net_total the customer has paid so far — net_total
            // minus paid_amount is the outstanding "due" amount.
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('branch_office_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
