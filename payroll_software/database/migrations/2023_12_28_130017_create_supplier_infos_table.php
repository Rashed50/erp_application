<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_infos', function (Blueprint $table) {
            $table->id('sup_auto_id');
            $table->string('sup_trade_name');
            $table->string('sup_vat');
            $table->string('sup_address')->nullable();
            $table->string('sup_mobile')->nullable();
            $table->boolean('sup_status')->default(1);  // 1= active, 0 = inactive
            $table->boolean('sup_type')->default(1);  // 0 = whole saler, 1= reseller          
            $table->integer('created_by');       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_infos');
    }
}
