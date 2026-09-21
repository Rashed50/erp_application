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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            // The ledger entry this purchase posted to the supplier's subsidiary
            // ledger, so an update/reversal here can keep that entry in sync
            // instead of drifting the way the source module's purchase edit did.
            $table->foreignId('supplier_transaction_id')->nullable()->constrained('supplier_transactions')->nullOnDelete();
            $table->string('purchase_type');
            $table->string('invoice_number')->unique();
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->default(0);
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
        Schema::dropIfExists('purchases');
    }
};
