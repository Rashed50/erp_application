<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emp_auto_id')->constrained()->onDelete('cascade');
            $table->string('pasfort_photo')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('akama_photo')->nullable();
            $table->string('medical_report')->nullable();
            $table->string('appoint_letter')->nullable();
            $table->string('covid_certificate')->nullable();
            $table->string('blood_group_paper')->nullable();
            $table->string('educational_papers')->nullable();
            $table->string('ajeer_file')->nullable();
            $table->string('signature_file')->nullable();
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
        Schema::dropIfExists('employee_files');
    }
}
