<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_activities', function (Blueprint $table) {
            
            $table->increments("ula_auto_id")->lenght(11);
            $table->smallInteger("ui_form_id");
            $table->smallInteger("uo_type_id");
            $table->integer("emp_auto_id")->lenght(8);
            $table->float("salary_amount",8,2)->nullable();
            $table->smallInteger("user_id");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_activities');
    }
}
