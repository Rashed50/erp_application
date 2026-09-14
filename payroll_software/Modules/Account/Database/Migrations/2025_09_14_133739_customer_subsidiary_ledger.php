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
         Schema::create('customer_subsidiary_ledger', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->autoIncrement();
            $table->string('customer_name')->comment(' Suplier Name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('vat_no')->nullable();
            $table->integer('payment_term')->default(20);
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('country')->nullable();
            $table->date('opening_date')->nullable();
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('active_status')->default(1);
            $table->integer('created_by')->comment('Created User Id');
            $table->integer('updated_by')->nullable()->comment('Updated User Id');
            $table->unsignedSmallInteger('branch_office_id')->default(1); //
            $table->timestamps();

             $table->index('customer_id');
            $table->index('vat_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
