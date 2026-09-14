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
        Schema::create('vehicle_servicing', function (Blueprint $table) {
            $table->unsignedBigInteger('veh_ser_auto_id', true);
            $table->unsignedBigInteger('veh_auto_id');
            $table->float('grand_total_amount',16,2);
            $table->float('discount',10,2);
            $table->float('payable_amount',16,2);
            $table->enum('payment_method',[1, 2])->default(2); // 1 Means 'Cash' and 1 means 'Bank'
            $table->boolean('servicing_status')->default(1);
            $table->date('start_date')->default(date("Y-m-d H:i:s"));
            //$table->date('')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('service_by')->nullable()->comment('employee ID');
            $table->string('invoice_file')->nullable();
            $table->string('invoice_no')->nullable();

            $table->string('remarks')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable()->comment('Approved User Id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Created User Id');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Updated User Id');
           // $table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            // Foreign key constraints
            $table->foreign('veh_auto_id')->references('veh_id')->on('vehicles');
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
        Schema::dropIfExists('table');

    }
};
