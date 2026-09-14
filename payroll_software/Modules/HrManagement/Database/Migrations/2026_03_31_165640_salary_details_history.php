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
       Schema::create('salary_details_hisotries', function (Blueprint $table) {
            $table->id('sdh_auto_id');
            $table->foreignId('emp_auto_id')->constrained('employee_infos', 'emp_auto_id')->onDelete('cascade');
            $table->integer('basic_amount')->default(0);
            $table->integer('basic_hours')->default(0);
            $table->integer('house_allowance')->default(0);
            $table->float('hourly_rate',6,2)->default(0);
            $table->integer('mobile_allowance')->default(0);
            $table->integer('medical_allowance')->default(0);
            $table->integer('travel_allowance')->default(0);
            $table->integer('conveyance_allowance')->default(0);
            $table->integer('increment_amount')->default(0);
            $table->integer('food_allowance')->default(0);
            $table->integer('cpf_contribution')->default(0);
            $table->integer('saudi_tax')->default(300);
            $table->integer('others1')->default(0);
            $table->integer('others4')->default(0);
            $table->tinyInteger('hourly_employee')->default(0);
            $table->string('payment_method')->default('cash');
            $table->foreignId('inserted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
