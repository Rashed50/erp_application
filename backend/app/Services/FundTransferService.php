<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FundTransfer;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FundTransferService
{
    public function __construct(private readonly LedgerPostingService $ledgerPostingService) {}

    public function paginate(int $perPage = 15, ?string $search = null, ?int $accountId = null, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return FundTransfer::query()
            ->with(['creditAccount:id,name,account_number', 'debitAccount:id,name,account_number', 'creator:id,name'])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            }))
            ->when($accountId, fn ($query) => $query->where(function ($query) use ($accountId) {
                $query->where('credit_account_id', $accountId)->orWhere('debit_account_id', $accountId);
            }))
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('transfer_date', [$fromDate, $toDate]))
            ->latest('transfer_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function find(FundTransfer $transfer): FundTransfer
    {
        return $transfer->load(['creditAccount:id,name,account_number', 'debitAccount:id,name,account_number', 'creator:id,name']);
    }

    /**
     * @param  array{credit_account_id: int, debit_account_id: int, receipt_no?: ?string, transfer_date: string, amount: float, bank_charge?: ?float, vat?: ?float, remarks?: ?string, attachment?: ?UploadedFile}  $data
     */
    public function create(array $data): FundTransfer
    {
        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];
            $bankCharge = (float) ($data['bank_charge'] ?? 0);
            $vat = (float) ($data['vat'] ?? 0);

            $transfer = FundTransfer::create([
                'credit_account_id' => $data['credit_account_id'],
                'debit_account_id' => $data['debit_account_id'],
                'receipt_no' => $data['receipt_no'] ?? null,
                'transfer_date' => $data['transfer_date'],
                'amount' => $amount,
                'bank_charge' => $bankCharge,
                'vat' => $vat,
                'total_amount' => $amount + $bankCharge + $vat,
                'remarks' => $data['remarks'] ?? null,
                'attachment' => isset($data['attachment']) ? $data['attachment']->store('fund-transfers', 'public') : null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->post($transfer, 1);

            return $this->find($transfer);
        });
    }

    /**
     * Undo the transfer's postings, then soft delete it.
     */
    public function delete(FundTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $this->post($transfer, -1);
            $transfer->delete();
        });
    }

    /**
     * Applies (direction 1) or reverses (direction -1) the transfer's double
     * entry: credit the sender with the total, debit the receiver with the
     * amount, and debit Bank Charges with the bank charge plus VAT.
     */
    private function post(FundTransfer $transfer, int $direction): void
    {
        $this->ledgerPostingService->credit(ChartOfAccount::findOrFail($transfer->credit_account_id), (float) $transfer->total_amount * $direction);
        $this->ledgerPostingService->debit(ChartOfAccount::findOrFail($transfer->debit_account_id), (float) $transfer->amount * $direction);

        if ($transfer->charges() > 0) {
            $bankCharges = $this->ledgerPostingService->predefinedAccount(LedgerPostingService::BANK_CHARGES_NUMBER)
                ?? throw new \RuntimeException('Predefined account '.LedgerPostingService::BANK_CHARGES_NUMBER.' is missing.');

            $this->ledgerPostingService->debit($bankCharges, $transfer->charges() * $direction);
        }
    }
}
