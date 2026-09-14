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
        Schema::create('subcontractor_services', function (Blueprint $table) {
            $table->unsignedBigInteger('subcon_service_auto_id', true);
            $table->unsignedBigInteger('subcon_auto_id');
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->float('no_of_unit',16,2);
            $table->float('per_unit_rate',16,2);
            $table->float('total_amount',16,2);
            $table->float('discount',10,2);
            $table->bigInteger('grand_total');
            $table->tinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('service_type',  [1,5,10])->default(1); // 1 = 'Manpower',  5='others' , 10 reserve
            $table->string('remarks')->nullable();
            $table->boolean('srv_status')->default(1);
            $table->string('service_invoice')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Approved User Id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Created User Id');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Updated User Id');
           // $table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            // Foreign key constraints
            $table->foreign('subcon_auto_id')->references('subcon_auto_id')->on('subcontractor_infos');

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
        Schema::dropIfExists('subcontractor_services');
    }
};
