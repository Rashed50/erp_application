<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_applications', function (Blueprint $table) { 

            $table->increments('leav_auto_id');
            $table->integer('emp_auto_id');
            $table->integer('leave_type_id');
            $table->integer('leave_reason_id');
            $table->integer('leav_days')->default(0); // in days
            $table->date('appl_date'); 
            $table->date('start_date');
            $table->date('end_date');
            $table->date('leave_start_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('return_date')->nullable();          
            $table->integer('approve_by')->nullable();
            $table->integer('inserted_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('reference_by')->nullable();
            $table->integer('appl_status')->default(0); // application submitted
            $table->text('description')->nullable();
            $table->text('admin_comments')->nullable();
            $table->string('leave_paper')->nullable();
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
        Schema::dropIfExists('emp_leaves');
    }
}
