<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_periods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->enum('status', ['open', 'submitted', 'approved', 'locked'])->default('open');
            $table->unsignedBigInteger('submitted_by_employee_auto_id')->nullable();
            $table->unsignedBigInteger('approved_by_employee_auto_id')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('submitted_by_employee_auto_id', 'attendance_periods_submitter_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('approved_by_employee_auto_id', 'attendance_periods_approver_fk')
                ->references('id')->on('employee_info')->cascadeOnUpdate()->nullOnDelete();
            $table->unique(['year', 'month'], 'attendance_periods_year_month_unique');
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_periods'); }
};
