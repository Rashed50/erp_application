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
        Schema::table('chartofacc_sales_customers', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
        });
        Schema::table('chartofacc_sales_records', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
        });
        Schema::table('sales_product_infos', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chartofacc_sales_customers', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
        Schema::table('chartofacc_sales_records', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
        Schema::table('sales_product_infos', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
    }
};
