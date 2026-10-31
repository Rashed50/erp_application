<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * More totals the models now compute instead of storing:
     * - sales.paid_amount: the customer's "Payment Received" entries carrying
     *   the invoice number.
     * - purchases.paid_amount: the purchase's bill payments.
     * - purchase_orders subtotal / discount / tax / grand_total: its lines.
     * - sale_items / purchase_items total_amount and purchase_order_items
     *   total: the line's qty x price - discount + VAT (tax).
     * - account_transactions.total_amount: the entry's debit lines.
     * - inventory_transfer_items sent_qty / received_qty: the requested
     *   quantity once the transfer is dispatched / received.
     *
     * @var array<string, array<int, string>>
     */
    private array $columns = [
        'sales' => ['paid_amount'],
        'purchases' => ['paid_amount'],
        'purchase_orders' => ['subtotal', 'discount', 'tax', 'grand_total'],
        'sale_items' => ['total_amount'],
        'purchase_items' => ['total_amount'],
        'purchase_order_items' => ['total'],
        'account_transactions' => ['total_amount'],
        'inventory_transfer_items' => ['sent_qty', 'received_qty'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->columns as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations: bring the columns back and fill them from the
     * rows they are worked out from.
     */
    public function down(): void
    {
        Schema::table('sales', fn (Blueprint $table) => $table->decimal('paid_amount', 15, 2)->default(0));
        DB::table('sales')->update(['paid_amount' => DB::raw("(select coalesce(sum(credit), 0) from customer_transactions where customer_transactions.transaction_type = 'Payment Received' and customer_transactions.status = 1 and customer_transactions.customer_id = sales.customer_id and customer_transactions.invoice_no = sales.invoice_number)")]);

        Schema::table('purchases', fn (Blueprint $table) => $table->decimal('paid_amount', 15, 2)->default(0));
        DB::table('purchases')->update(['paid_amount' => DB::raw('(select coalesce(sum(bill_amount), 0) from supplier_payments where supplier_payments.purchase_id = purchases.id and supplier_payments.deleted_at is null)')]);

        foreach (['sale_items', 'purchase_items'] as $tableName) {
            Schema::table($tableName, fn (Blueprint $table) => $table->decimal('total_amount', 15, 2)->default(0));
            DB::table($tableName)->update(['total_amount' => DB::raw('qty * unit_price - discount + vat')]);
        }

        Schema::table('purchase_order_items', fn (Blueprint $table) => $table->decimal('total', 15, 2)->default(0));
        DB::table('purchase_order_items')->update(['total' => DB::raw('quantity * unit_price - discount + tax')]);

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
        });
        $sum = fn (string $expression) => "(select coalesce(sum({$expression}), 0) from purchase_order_items where purchase_order_items.purchase_order_id = purchase_orders.id)";
        DB::table('purchase_orders')->update([
            'subtotal' => DB::raw($sum('round(quantity * unit_price, 2)')),
            'discount' => DB::raw($sum('discount')),
            'tax' => DB::raw($sum('tax')),
        ]);
        DB::table('purchase_orders')->update(['grand_total' => DB::raw('subtotal - discount + tax')]);

        Schema::table('account_transactions', fn (Blueprint $table) => $table->decimal('total_amount', 15, 2)->default(0));
        DB::table('account_transactions')->update(['total_amount' => DB::raw('(select coalesce(sum(debit), 0) from account_transaction_details where account_transaction_details.account_transaction_id = account_transactions.id)')]);

        Schema::table('inventory_transfer_items', function (Blueprint $table) {
            $table->decimal('sent_qty', 15, 3)->default(0);
            $table->decimal('received_qty', 15, 3)->default(0);
        });
        DB::table('inventory_transfer_items')
            ->whereIn('inventory_transfer_id', DB::table('inventory_transfers')->whereIn('status', ['in_transit', 'received'])->select('id'))
            ->update(['sent_qty' => DB::raw('requested_qty')]);
        DB::table('inventory_transfer_items')
            ->whereIn('inventory_transfer_id', DB::table('inventory_transfers')->where('status', 'received')->select('id'))
            ->update(['received_qty' => DB::raw('requested_qty')]);
    }
};
