<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        //  ALTER TABLE `users` ADD COLUMN `emp_auto_id` int null
        // ALTER TABLE `users` ADD COLUMN `is_emp` bool DEFAULT 0;
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone_number',20)->unique();
            $table->string('email')->unique()->nullable();
            $table->boolean('status')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('emp_auto_id')->nullable(); // Column add without run migration
            $table->boolean('is_emp', [0,1])->default(0); // Column add without run migration
            $table->integer('branch_office_id')->default(1); // Column add without run migration
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
