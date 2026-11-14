<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A previously received advance (or earlier bill balance) applied against
        // this sale. It lowers the payable amount, but is neither a discount nor
        // a new payment.
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('previous_advance_amount', 15, 2)->default(0)->after('overall_vat');
            $table->string('previous_advance_purpose')->nullable()->after('previous_advance_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['previous_advance_amount', 'previous_advance_purpose']);
        });
    }
};
