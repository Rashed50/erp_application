<?php

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Brings existing books in line with the corrected posting rules:
     *
     * - Customers and suppliers deleted before deletion reversed their
     *   opening balance entry still count in Accounts Receivable/Payable;
     *   each such entry gets its reversal entry, dated today.
     * - Ledger accounts created with an opening balance that was never
     *   posted (every non-asset account) get their opening entry, dated on
     *   the account's opening date.
     *
     * Both steps skip what is already done, so this is safe to re-run.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $this->reverseDeletedPartyEntries();
            $this->postMissingOpeningEntries();
        });
    }

    /**
     * Reverse the migrations. The entries it posted are ordinary journal
     * history and are left in place.
     */
    public function down(): void {}

    private function reverseDeletedPartyEntries(): void
    {
        foreach (['customers' => Customer::class, 'suppliers' => Supplier::class] as $table => $model) {
            $entries = DB::table('account_transactions')
                ->where('source_type', (new $model)->getMorphClass())
                ->whereIn('source_id', DB::table($table)->whereNotNull('deleted_at')->select('id'))
                ->whereNull('reversal_of_id')
                ->whereNotExists(fn ($query) => $query->from('account_transactions as reversals')
                    ->whereColumn('reversals.reversal_of_id', 'account_transactions.id'))
                ->get();

            foreach ($entries as $entry) {
                $lines = DB::table('account_transaction_details')->where('account_transaction_id', $entry->id)->get();

                $this->insertEntry([
                    'tr_no' => $entry->tr_no,
                    'date' => today()->toDateString(),
                    'general_particular' => $table === 'customers' ? 'reverse deleted Customer Sales account' : 'reverse deleted supplier account',
                    'source_type' => $entry->source_type,
                    'source_id' => $entry->source_id,
                    'reversal_of_id' => $entry->id,
                ], $lines->map(fn (object $line) => [
                    'chart_of_account_id' => $line->chart_of_account_id,
                    'debit' => $line->credit,
                    'credit' => $line->debit,
                    'particular' => "Reversal: {$line->particular}",
                ])->all());
            }
        }
    }

    private function postMissingOpeningEntries(): void
    {
        $equity = DB::table('chart_of_accounts')
            ->where('account_type_id', AccountType::OWNER_EQUITY)
            ->whereNull('parent_id')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->first();

        if (! $equity) {
            return;
        }

        $accounts = DB::table('chart_of_accounts')
            ->join('account_types', 'account_types.id', '=', 'chart_of_accounts.account_type_id')
            ->whereNull('chart_of_accounts.deleted_at')
            ->where('chart_of_accounts.opening_balance', '>', 0)
            ->where('chart_of_accounts.id', '!=', $equity->id)
            ->whereNotExists(fn ($query) => $query->from('account_transactions')
                ->where('account_transactions.source_type', (new ChartOfAccount)->getMorphClass())
                ->whereColumn('account_transactions.source_id', 'chart_of_accounts.id'))
            ->get(['chart_of_accounts.*', 'account_types.normal_balance']);

        foreach ($accounts as $account) {
            $amount = (float) $account->opening_balance;
            $onDebit = $account->normal_balance === 'debit';

            $this->insertEntry([
                'date' => substr((string) ($account->opening_date ?? today()->toDateString()), 0, 10),
                'general_particular' => 'Initial Ledger account setup',
                'source_type' => (new ChartOfAccount)->getMorphClass(),
                'source_id' => $account->id,
            ], [
                [
                    'chart_of_account_id' => $account->id,
                    'debit' => $onDebit ? $amount : 0,
                    'credit' => $onDebit ? 0 : $amount,
                    'particular' => $onDebit ? "{$amount} amt dr from {$account->name}" : "{$amount} amt cr from {$account->name}",
                ],
                [
                    'chart_of_account_id' => $equity->id,
                    'debit' => $onDebit ? 0 : $amount,
                    'credit' => $onDebit ? $amount : 0,
                    'particular' => $onDebit ? "{$amount} amt cr from {$equity->name}" : "{$amount} amt dr from {$equity->name}",
                ],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $header
     * @param  array<int, array{chart_of_account_id: int, debit: float|string, credit: float|string, particular: ?string}>  $lines
     */
    private function insertEntry(array $header, array $lines): void
    {
        $now = now();
        $entryId = DB::table('account_transactions')->insertGetId([...$header, 'created_at' => $now, 'updated_at' => $now]);

        DB::table('account_transaction_details')->insert(array_map(
            fn (array $line) => [...$line, 'account_transaction_id' => $entryId, 'created_at' => $now, 'updated_at' => $now],
            $lines,
        ));
    }
};
