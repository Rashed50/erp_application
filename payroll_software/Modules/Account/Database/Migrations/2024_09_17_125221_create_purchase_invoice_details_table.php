<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_invoice_details', function (Blueprint $table) {
            $table->id('pur_det_id');
            $table->timestamps();

            $table->unsignedBigInteger('purchase_id')->comment('Purchase Invoice Id');
            $table->unsignedBigInteger('item_id')->nullable()->comment('Item Names Id');
            $table->string('service_name')->nullable();

            $table->text('description')->nullable();
            $table->decimal('qty', 15, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('purchase_id')->references('pur_id')->on('purchase_invoice')->onDelete('cascade');
           // $table->foreign('item_id')->references('item_id')->on('item_names')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_invoice_details');
    }
};
