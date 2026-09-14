<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payslip_email_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_auto_id')->index();
            $table->string('employee_id')->nullable();
            $table->string('email')->nullable();
            $table->integer('month');
            $table->integer('year');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Prevent duplicate logs for same employee/month/year
            $table->unique(['emp_auto_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_email_logs');
    }
};
