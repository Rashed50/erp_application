<?php

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
        Schema::create('overtime_sheets', function (Blueprint $table) {
            $table->id('ots_auto_id');
            $table->integer('project_id');
            $table->date('ot_date');
            $table->integer('approved_by')->nullable();
            $table->integer('insert_by');
            $table->string('ot_file')->nullable();
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
        Schema::dropIfExists('overtime_sheets');
    }
};
