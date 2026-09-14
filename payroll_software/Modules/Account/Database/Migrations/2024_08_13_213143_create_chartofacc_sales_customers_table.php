<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('chartofacc_sales_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('cus_auto_id',true);
            $table->string('cus_name',100);
            $table->string('cus_phone',20)->unique();
            $table->string('cus_email',100)->nullable();
            $table->string('cus_tax_number')->unique();
            $table->boolean('cus_status')->default(1);
            $table->integer('created_by_id');
            $table->integer('updated_by_id')->nullable();

            $table->string('cus_org_name')->nullable();
            $table->string('cus_billing_address')->nullable();
            $table->string('cus_billing_city')->nullable();
            $table->string('cus_billing_state')->nullable();
            $table->string('cus_billing_zip')->nullable();
            $table->string('cus_billing_country')->nullable();

            $table->timestamps();
        });

        DB::table('chartofacc_sales_customers')->insert([ // step 01
            'cus_name' => 'Customer-1',
            'cus_phone' => '0126588',
            'cus_tax_number' => '145263',
            'created_by_id' => 1
          ]);

          DB::table('chartofacc_sales_customers')->insert([ // step 01
            'cus_name' => 'Customer-2',
            'cus_phone' => '0175426588',
            'cus_tax_number' => '123456',
            'created_by_id' => 1
          ]);

          DB::table('chartofacc_sales_customers')->insert([ // step 01
            'cus_name' => 'Customer-3',
            'cus_phone' => '852963577',
            'cus_tax_number' => '85278987',
            'created_by_id' => 1
          ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartofacc_sales_customers');
    }
};
