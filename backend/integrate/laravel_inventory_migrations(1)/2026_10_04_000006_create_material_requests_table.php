<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 50)->unique();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->date('request_date');
            $table->date('required_date')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', [
                'draft', 'submitted', 'approved', 'partially_purchased',
                'purchased', 'partially_issued', 'completed', 'rejected', 'cancelled'
            ])->default('draft');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('proj_id')->on('project_infos')
                ->restrictOnDelete();

            $table->foreign('requested_by')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('emp_auto_id')->on('employee_infos')
                ->nullOnDelete();

            $table->index(['project_id', 'status']);
            $table->index('request_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
