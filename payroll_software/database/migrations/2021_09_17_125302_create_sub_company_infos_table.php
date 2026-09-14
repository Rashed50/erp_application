<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCompanyInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_company_infos', function (Blueprint $table) {
            $table->id('sb_comp_id');
            $table->string('sb_comp_name',150);
            $table->string('sb_comp_name_arb',150);
            $table->integer('company_id');
            $table->text('sb_comp_address'); 
            $table->string('sb_comp_mobile1',20);
            $table->string('sb_vat_no',50);
            $table->string('sb_comp_email1',50);
            $table->string('sb_comp_email2',50);
            $table->string('sb_comp_phone1',30);
            $table->string('sb_comp_phone2',30);
            $table->text('sb_comp_contact_parson_details');
            $table->integer('entered_id');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('sub_company_infos');
    }
}
