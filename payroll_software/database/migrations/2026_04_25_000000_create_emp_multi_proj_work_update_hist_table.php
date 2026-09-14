<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpMultiProjWorkUpdateHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_multi_proj_work_update_hist', function (Blueprint $table) {
            $table->bigIncrements('empwh_auto_id');
            $table->unsignedBigInteger('emp_id'); // emp_auto_id of employee_infos table
           //  $table->foreignId('emp_auto_id')->constrained('employee_infos', 'emp_auto_id')->onDelete('cascade');
            // Period Information
            $table->integer('month')->comment('Month (1-12)');
            $table->integer('year')->comment('Year (YYYY)');
            $table->integer('project_id')->comment('Project ID reference');

            // Work Details
            $table->integer('total_day')->comment('Total working days');
            $table->integer('paid_leave')->default(0)->comment('Paid leave days');
            $table->float('total_hour')->comment('Total working hours');
            $table->float('total_overtime')->default(0)->comment('Total overtime hours');

            // Financial Details
            $table->float('total_amount')->default(0)->comment('Total salary amount');
            $table->float('food_amount')->default(0)->comment('Food allowance amount');
            $table->float('other_amount')->default(0)->comment('Other allowances amount');
            $table->float('ot_amount')->default(0)->comment('Overtime amount');
            $table->integer('update_by_id')->comment('users  ID reference');
            // Audit & Tracking
            // $table->foreignId('update_by_id')
            //       ->constrained('users')
            //       ->onDelete('set null')
            //       ->comment('User who performed the update');

            $table->integer('branch_office_id')->default(1)->comment('Branch office identifier');

            // Date Tracking
            $table->date('start_date')->nullable()->comment('Period start date');
            $table->date('end_date')->nullable()->comment('Period end date');
            $table->string('operation_type', 15)->nullable()->comment('UPDATE/DELETE operation type');

            // Timestamps
            $table->timestamps();


            // Unique index to prevent duplicate entries for the same employee, month, year and project
            $table->unique(
                ['emp_id', 'month', 'year', 'project_id'],
                'unique_multi_project_work_record_per_month'
            );

            // Additional indexes for better query performance
            $table->index(['month', 'year'], 'idx_month_year');
            $table->index(['project_id', 'branch_office_id'], 'idx_project_branch');
            $table->index('update_by_id', 'idx_updated_by');

            // Composite index for common search patterns
            $table->index(['emp_id', 'year', 'month'], 'idx_emp_year_month');
        });

        // Schema::create('emp_multi_proj_work_update_hist', function (Blueprint $table) {
        //     $table->bigIncrements('empwh_auto_id');
        //     $table->foreignId('emp_id')->constrained('employee_infos', 'emp_auto_id')->onDelete('set null');
        //     $table->integer('month');
        //     $table->integer('year');
        //     $table->integer('project_id');
        //     $table->integer('total_day');
        //     $table->integer('paid_leave')->default(0);
        //     $table->float('total_hour');
        //     $table->float('total_overtime')->default(0);
        //     $table->float('total_amount')->default(0);
        //     $table->float('food_amount')->default(0);
        //     $table->float('other_amount')->default(0);
        //     $table->float('ot_amount')->default(0);
        //     $table->foreignId('update_by_id')->constrained('users')->onDelete('set null');
        //     $table->integer('branch_office_id')->default(1);
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->string('operation_type')->nullable();
        //     $table->timestamps();

        //      // unique index to prevent duplicate entries for the same employee, month, and year
        //     $table->unique(['emp_id', 'month', 'year','project_id'], 'unique_multi_project_work_record_per_month');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_multi_proj_work_update_hist');
    }
}
