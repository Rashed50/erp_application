<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartof_account_types', function (Blueprint $table) {
            $table->increments('acct_type_id');
            $table->string('acct_type_name');
            $table->timestamps();
        });

          DB::table('chartof_account_types')->insert([ // step 01
            'acct_type_name' => 'Asset'
          ]);

          DB::table('chartof_account_types')->insert([ // step 02
            'acct_type_name' => 'Liability'
          ]);

          DB::table('chartof_account_types')->insert([ // step 03
            'acct_type_name' => 'Owner Equity'
          ]);

          DB::table('chartof_account_types')->insert([ // step 04
            'acct_type_name' => 'Revenue'
          ]);
          DB::table('chartof_account_types')->insert([ // step 05
            'acct_type_name' => 'Expense'
          ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_types');
    }
}
