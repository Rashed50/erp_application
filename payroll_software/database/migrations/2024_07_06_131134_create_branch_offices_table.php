<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_offices', function (Blueprint $table) {
            $table->increments("braoff_auto_id");
            $table->string("branch_code");
            $table->string("branch_name_en");
            $table->string("branch_name_native");
            $table->boolean("branch_status")->default(1);
            $table->string("branch_address")->nullable();
            $table->string("branch_contact_no")->nullable();
            $table->smallInteger("branch_currency_id");
            $table->unsignedMediumInteger("created_by");
            $table->unsignedMediumInteger("updated_by")->nullable();
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
        Schema::dropIfExists('branch_offices');
    }
}
