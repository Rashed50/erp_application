<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventorySuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_suppliers', function (Blueprint $table) {
            $table->id('isupp_auto_id');
            $table->string('isupp_name');
            $table->string('isupp_email');
            $table->string('isupp_vat_number');
            $table->string('isupp_contact_address')->nullable();
            $table->integer('branch_office_id')->default(1);
            $table->boolean("isupp_status")->default(1);
            $table->smallInteger('isupp_create_by_id');
            $table->smallInteger('isupp_update_by_id')->nullable();
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
        Schema::dropIfExists('inventory_suppliers');
    }
}
