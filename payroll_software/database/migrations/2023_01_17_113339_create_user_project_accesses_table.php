<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProjectAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_project_accesses', function (Blueprint $table) {
            $table->id('user_proj_acc_auto_id');
            $table->integer('user_auto_id');
            $table->integer('proj_id');
            $table->integer('insert_by');
            $table->integer('update_by')->nullable();
            $table->boolean('access_status', [0,1])->default(1);
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
        Schema::dropIfExists('user_project_accesses');
    }
}
