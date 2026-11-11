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
        // Audit trail of every salary revision added, edited or deleted: the
        // old and new value of each field that changed, who did it and when.
        Schema::create('salary_detail_logs', function (Blueprint $table) {
            $table->id();
            // Kept (as NULL) after the revision itself is deleted.
            $table->foreignId('salary_detail_id')->nullable()->constrained('salary_details')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('employee_info')->restrictOnDelete();
            $table->string('action', 20);
            $table->json('changes');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['employee_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_detail_logs');
    }
};
