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
        Schema::create('employee_bonus_records', function (Blueprint $table) {
            $table->id('bonus_auto_id');
            $table->integer('emp_auto_id');
            $table->integer('bonus_type');
            $table->integer('month');
            $table->integer('year');
            $table->float('amount');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_bonus_types');
    }
};
