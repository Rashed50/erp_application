<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartofaccountPurchaseItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartofaccount_purchase_item_details', function (Blueprint $table) {
            $table->bigIncrements('ca_purch_itm_auto_id');
            $table->bigInteger('ca_purch_auto_id');

            $table->string('cpid_description');
            $table->integer('item_deta_id');
            $table->float('cpid_qty',11,2);
            $table->float('cpid_unit_price',11,2);
            $table->boolean('cpid_inclusive')->default(0);
            $table->float('cpid_discount',11,2)->default(0);
            $table->string('cpid_vat_percent')->default(0);
            $table->float('cpid_vat_value',11,2)->default(0);
            $table->float('cpid_paid_amount',11,2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ca_purch_auto_id')->references('ca_purch_auto_id')->on('chartofaccount_purchase_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartofaccount_purchase_item_details');
    }
}
