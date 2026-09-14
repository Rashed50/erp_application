<?php

//use Predis\Command\Traits\DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_account_types', function (Blueprint $table) {
            $table->increments('bank_acct_type_id');
            $table->string('ban_acc_type_name');
            $table->boolean("ban_acc_type_status")->default(1);
            $table->timestamps();
        });

          DB::table('bank_account_types')->insert([ // step 02
            'ban_acc_type_name' => 'Current Accounts'
          ]);

          DB::table('bank_account_types')->insert([ // step 01
            'ban_acc_type_name' => 'Saving Accounts'
         ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_account_types');
    }
}
