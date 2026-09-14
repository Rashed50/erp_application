<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpTUVInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_t_u_v_infos', function (Blueprint $table) {
            $table->id('tuv_auto_id');
            $table->integer('emp_auto_id');
            $table->string('card_no');
            $table->integer('trade_id');
            $table->integer('company_id');
            $table->date('issue_date');
            $table->date('expire_date');
            $table->string('emp_tuv_photo',100)->nullable();
            $table->boolean('tuv_status')->default(1);
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
        Schema::dropIfExists('emp_t_u_v_infos');
    }
}
