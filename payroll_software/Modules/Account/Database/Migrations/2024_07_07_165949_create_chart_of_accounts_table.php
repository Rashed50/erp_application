<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id('chart_of_acct_id');
            $table->string('chart_of_acct_name');
            $table->string('chart_of_acct_number')->nullable();

            $table->integer('account_id')->default(0); // sub account id


            
            $table->integer('sibling_level')->default(0); // 0 = parent, 1 = child, 2 = grand child and so on
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('acct_balance')->default(0);
            $table->date('opening_date');
            $table->boolean('active_status')->default(true);
            $table->boolean('is_transaction')->default(false);
            $table->boolean('is_predefined')->default(false);
            $table->boolean('is_closed')->default(false);

            $table->unsignedBigInteger('acct_type_id'); // Asset, Liability, OE, Rev, Expense

            $table->smallInteger('created_by_id');
            $table->smallInteger('updated_by_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('chart_of_acct_id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('acct_type_id')->references('acct_type_id')->on('chartof_account_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_of_accounts');
    }
}
