<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id('inv_stock_id');
            $table->integer('item_deta_id');
            $table->integer('inv_prev_stock');
            $table->integer('inv_current_stock');
            $table->integer('inv_year');
            $table->date('inv_date');
            $table->date('inv_start_date');
            $table->date('inv_end_date');
            $table->boolean('inv_stock_status')->default(1);
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
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
        Schema::dropIfExists('inventory_stocks');
    }
}
