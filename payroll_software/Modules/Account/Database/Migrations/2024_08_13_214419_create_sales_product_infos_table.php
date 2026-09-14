<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('sales_product_infos', function (Blueprint $table) {
            $table->increments('spi_auto_id');
            $table->string('spi_name_en');
            $table->string('spi_name_ab')->nullable();
            $table->string('spi_code')->unique();
            $table->boolean('spi_status')->default(1);
            $table->integer('created_by_id');
            $table->integer('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('sales_product_infos')->insert([ // step 01
            'spi_name_en' => 'Product-1',
            'spi_code' => '100',
            'created_by_id' => 1
          ]);

          DB::table('sales_product_infos')->insert([ // step 01
            'spi_name_en' => 'Product-2',
            'spi_code' => '102',
            'created_by_id' => 1
          ]);

          DB::table('sales_product_infos')->insert([ // step 01
            'spi_name_en' => 'Product-3',
            'spi_code' => '103',
            'created_by_id' => 1
          ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_product_infos');
    }
};
