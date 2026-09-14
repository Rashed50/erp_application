<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySheetUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_sheet_uploads', function (Blueprint $table) {
            $table->bigIncrements('ss_auto_id');
            $table->integer('no_of_emp');
            $table->float('total_salary',10);
            $table->date('salary_date');
            $table->integer('month');
            $table->integer('year');
            $table->integer('spons_id');
            $table->integer('proj_od');
            $table->string('remarks',200);
            $table->string('file_path',200);
            $table->string('file_name',200);
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
        Schema::dropIfExists('salary_sheet_uploads');
    }
}
