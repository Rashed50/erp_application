<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_infos', function (Blueprint $table) {
            $table->id('emp_auto_id');
            $table->integer('employee_id');
            $table->string('employee_name',50);
            $table->string('passfort_no',50);
            $table->date('passfort_expire_date');

            $table->string('akama_no',50);
            $table->date('akama_expire_date');
            $table->integer('company_id'); // new column name added without run migration
            $table->integer('sponsor_id'); // add korte hobe
            $table->string('mobile_no',30);

            $table->integer('country_id')->nullable()->default(1);
            $table->integer('division_id')->nullable()->default(13);
            $table->integer('district_id')->nullable()->default(6);
            $table->string('post_code',50)->nullable()->default(1340);
            $table->string('details',255)->nullable();
            $table->string('present_address')->nullable();
            $table->integer('emp_type_id')->nullable()->default(1);
            $table->integer('project_id')->nullable();
            // $table->integer('catg_type_id');
            $table->integer('designation_id')->nullable();
            $table->boolean('hourly_employee')->nullable();
            $table->integer('department_id')->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('phone_no',50)->nullable();
            $table->string('email',50)->nullable();
            $table->integer('maritus_status')->default(1);
            $table->string('gender',1)->nullable()->default(1);
            $table->integer('religion')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('appointment_date')->nullable();
          //  $table->string('job_type',20)->nullable();
            $table->boolean('job_status')->default(0);
            $table->string('job_location',150)->nullable();
            $table->date('entry_date')->nullable();
            $table->string('pasfort_photo',100)->nullable();
            $table->string('profile_photo',100)->nullable();
            $table->string('akama_photo',100)->nullable();
            $table->string('medical_report',100)->nullable();
            $table->string('employee_appoint_latter')->nullable();
            $table->integer('entered_id');
            $table->integer('salary_status')->default(1);            
            $table->boolean('isNightShift', [1,0])->default(0); // new column name added without run migration
            $table->boolean('accomd_ofb_id')->default(1); // new column name added without run migration
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
        Schema::dropIfExists('employee_infos');
    }
}
