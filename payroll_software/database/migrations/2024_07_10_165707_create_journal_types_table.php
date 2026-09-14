<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_types', function (Blueprint $table) {
            $table->id('jour_type_id');
            $table->string('jour_type_name');
            $table->timestamps();
        });

        // insert some default data into this table
        DB::table('journal_types')->insert([
            'jour_type_name' => 'Sales'
        ]);
        DB::table('journal_types')->insert([
            'jour_type_name' => 'Purchase'
        ]);
        DB::table('journal_types')->insert([
            'jour_type_name' => 'Bank'
        ]);
        DB::table('journal_types')->insert([
            'jour_type_name' => 'Cash'
        ]);
        DB::table('journal_types')->insert([
            'jour_type_name' => 'Miscellaneous'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_types');
    }
}
