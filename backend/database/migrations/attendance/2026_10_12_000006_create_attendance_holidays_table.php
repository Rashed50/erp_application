<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_holidays', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('holiday_date');
            $table->string('name', 150);
            $table->enum('holiday_type', ['public', 'company', 'project'])->default('company');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->boolean('is_paid')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            // Add a project FK only after verifying the exact existing project schema.
            $table->unique(['holiday_date', 'project_id', 'holiday_type'], 'attendance_holidays_date_project_type_unique');
            $table->index('holiday_date');
        });
    }

    public function down(): void { Schema::dropIfExists('attendance_holidays'); }
};
