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
        Schema::create('subcontractor_infos', function (Blueprint $table) {
            
            $table->unsignedBigInteger('subcon_auto_id', true);
            $table->unsignedBigInteger('sponsor_id');
            $table->string('id_number')->nullable();
            $table->string('subcon_name');
            $table->string('passfort_no');
            $table->date('pass_expire');
            $table->string('iqama_no');
            $table->date('iqama_expire');
            $table->string('mobile_no', 20);
            $table->string('abshar_mobile_no', 20);
            $table->string('country_contact_no', 20);

            $table->unsignedSmallInteger('country_id');
            $table->unsignedSmallInteger('division_id');
            $table->unsignedSmallInteger('district_id')->nullable();
            $table->string('post_code', 50)->nullable();
            $table->string('details', 255)->nullable();
            $table->string('present_address')->nullable();
            $table->string('subcont_email', 50)->nullable();
            $table->date('joining_date')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('remarks')->nullable();
            $table->string('iqama_file')->nullable();
            $table->string('passport_file')->nullable();
            $table->string('contract_paper')->nullable();
            $table->boolean('act_status')->default(1);
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Approved User Id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Created User Id');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Updated User Id');
            // $table->timestamps();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            // Foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subcontractor_infos');
    }
};
