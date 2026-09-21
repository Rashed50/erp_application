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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            // A customer with ledger transactions must never be deleted, since that
            // would destroy accounting history, so this is restricted rather than
            // cascaded.
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('transaction_type');
            $table->string('invoice_no')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('branch_office_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
