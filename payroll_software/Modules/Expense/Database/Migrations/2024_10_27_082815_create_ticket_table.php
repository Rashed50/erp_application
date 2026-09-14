<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_auto_id', true); 

            $table->enum('ticket_for',  ['employee', 'others'])->default('employee');
            $table->unsignedBigInteger('emp_auto_id')->nullable()->comment('Employee Id');
            $table->unsignedBigInteger('guest_auto_id')->nullable()->comment('Guest/Others Id');

            $table->enum('ticket_type', ['one_way', 'return'])->nullable();
            $table->enum('paid_by',     [2, 1])->default(2); // 2 Means 'company' and 1 means 'self'

            $table->string('ticket_number')->nullable();
            $table->date('confirm_date');

            $table->unsignedSmallInteger('qty')->default(1);
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->decimal('total_price', 8, 2)->default(0);
            $table->string('remarks')->nullable();
            $table->string('reference_by')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_approved')->default(0);

            $table->unsignedBigInteger('approved_by')->nullable()->comment('Approved User Id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Created User Id');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Updated User Id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket');
    }
};
