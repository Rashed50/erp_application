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
        Schema::create('subcontractors_payment', function (Blueprint $table) {
            $table->unsignedBigInteger('subcon_pay_auto_id', true);
            $table->unsignedBigInteger('subcon_auto_id');
            $table->float('total_amount',16,2);
            $table->float('discount',10,2);
            $table->bigInteger('grand_total');
            $table->tinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('payment_method',[1, 2])->default(2); // 1 Means 'Cash' and 1 means 'Bank'
            $table->smallInteger('bank_id')->nullable();
            $table->date('payment_date');
            $table->string('payment_type')->nullable();
            $table->string('remarks')->nullable();
            $table->string('payment_file')->nullable();
            $table->boolean('act_status')->default(0); //0 = created, 3 review, 10 final aceptance and approved
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
        Schema::dropIfExists('table');
    }
};
