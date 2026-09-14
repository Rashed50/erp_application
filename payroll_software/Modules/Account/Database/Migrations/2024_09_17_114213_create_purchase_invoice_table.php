<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('pur_id',true);
            $table->timestamps();
            $table->enum('purchase_type', ['product', 'service'])->default('product');

            $table->text('description')->nullable();
            $table->unsignedBigInteger('supplier_id')->comment('Inventory Suppliers Id');
            $table->dateTime('issue_date');
            $table->date('purchase_date');
            $table->string('invoice_number')->unique();

            $table->decimal('total_amount', 15, 2)->default(0)->comment('Total amount without vat');
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->comment('Total after VAT and discount');

            $table->unsignedBigInteger('account_debit_id')->nullable();
            $table->unsignedBigInteger('account_credit_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            $table->text('notes')->nullable()->comment('Additional notes or remarks');
            $table->integer('created_by')->comment('Created User Id');
            $table->integer('updated_by')->nullable()->comment('Updated User Id');
            $table->unsignedBigInteger('branch_id')->nullable(); // it should be name of branch_office_id
            $table->softDeletes();

            // Foreign key constraints
          //  $table->foreign('supplier_id')->references('isupp_auto_id')->on('inventory_suppliers')->onDelete('cascade');
         //   $table->foreign('created_by')->references('id')->on('users');
          //  $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_invoice');
    }
};
