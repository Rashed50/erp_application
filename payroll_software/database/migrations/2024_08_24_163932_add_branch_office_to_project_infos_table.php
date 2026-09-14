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
        Schema::table('project_infos', function (Blueprint $table) {
            $table->foreignId('branch_office_id')->nullable();
            $table->string('working_status')->nullable();
        });
        Schema::table('employee_infos', function (Blueprint $table) {
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
        Schema::table('project_infos', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
            $table->dropColumn('working_status');
        });
        Schema::table('employee_infos', function (Blueprint $table) {
            $table->dropColumn('branch_office_id');
        });
    }
};
