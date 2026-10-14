<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_auto_id');
            $table->unsignedBigInteger('attendance_shift_id');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->foreign('employee_auto_id', 'employee_shifts_employee_fk')
                ->references('id')->on('employee_info')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('attendance_shift_id', 'employee_shifts_shift_fk')
                ->references('id')->on('attendance_shifts')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->index(['employee_auto_id', 'effective_from', 'effective_to'], 'employee_shifts_employee_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_shifts');
    }
};
