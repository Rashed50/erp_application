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
        Schema::create('chartofacc_sales_record_details', function (Blueprint $table) {
            $table->increments('srd_id');
            $table->unsignedBigInteger('sr_auto_id');
            $table->unsignedBigInteger('srd_product_id')->nullable();
            $table->foreign('sr_auto_id')->references('sr_auto_id')->on('chartofacc_sales_records');
            $table->string('srd_description');
            $table->float('srd_qty',11,2);
            $table->float('srd_unit_price',11,2);
            $table->boolean('srd_inclusive');
            $table->float('srd_discount',11,2);
            $table->string('srd_vat_percent');
            $table->float('srd_vat_value',11,2);
            $table->float('srd_total_amount',11,2);
          //  $table->foreign('srd_product_id')->references('id')->on('products');
          //  $table->foreignId('srd_product_id')->nullable()->constrained('products');
            $table->string('product_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartofacc_sales_record_details');
    }
};
