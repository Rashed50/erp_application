<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentService
{
    public function __construct(
        private readonly SupplierTransactionService $supplierTransactionService,
        private readonly LedgerPostingService $ledgerPostingService,
    ) {}

    public function paginate(
        int $perPage = 15,
        ?string $search = null,
        ?int $supplierId = null,
        ?string $fromDate = null,
        ?string $toDate = null,
    ): LengthAwarePaginator {
        return SupplierPayment::query()
            ->with(['supplier:id,name', 'paymentAccount:id,name,account_number', 'purchase:id,invoice_number', 'creator:id,name'])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            }))
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->when($fromDate && $toDate, fn ($query) => $query->whereBetween('payment_date', [$fromDate, $toDate]))
            ->latest('payment_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function find(SupplierPayment $payment): SupplierPayment
    {
        return $payment->load(['supplier:id,name', 'paymentAccount:id,name,account_number', 'purchase:id,invoice_number', 'creator:id,name']);
    }

    /**
     * Record a bill payment: post it to the supplier ledger, settle the linked
     * purchase (if any), and post the double entry to the chart of accounts.
     *
     * @param  array{supplier_id: int, purchase_id?: ?int, payment_account_id: int, invoice_no?: ?string, payment_date: string, bill_amount: float, bank_charge?: ?float, remarks?: ?string, attachment?: ?UploadedFile}  $data
     */
    public function create(array $data): SupplierPayment
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::findOrFail($data['supplier_id']);
            $purchase = isset($data['purchase_id']) ? Purchase::findOrFail($data['purchase_id']) : null;
            $billAmount = (float) $data['bill_amount'];
            $bankCharge = (float) ($data['bank_charge'] ?? 0);
            $invoiceNo = $purchase?->invoice_number ?? ($data['invoice_no'] ?? null);

            $transaction = $this->supplierTransactionService->create($supplier, [
                'transaction_type' => 'Bill Payment',
                'invoice_no' => $invoiceNo,
                'debit' => $billAmount,
                'transaction_date' => $data['payment_date'],
                'notes' => $data['remarks'] ?? null,
            ]);

            $purchase?->update([
                'paid_amount' => $purchase->paid_amount + $billAmount,
                'updated_by' => Auth::id(),
            ]);

            $payment = SupplierPayment::create([
                'supplier_id' => $supplier->id,
                'purchase_id' => $purchase?->id,
                'payment_account_id' => $data['payment_account_id'],
                'supplier_transaction_id' => $transaction->id,
                'invoice_no' => $invoiceNo,
                'payment_date' => $data['payment_date'],
                'bill_amount' => $billAmount,
                'bank_charge' => $bankCharge,
                'total_amount' => $billAmount + $bankCharge,
                'remarks' => $data['remarks'] ?? null,
                'attachment' => isset($data['attachment']) ? $data['attachment']->store('supplier-payments', 'public') : null,
                'branch_office_id' => $supplier->branch_office_id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->post($payment, 1);

            return $this->find($payment);
        });
    }

    /**
     * Undo everything the payment posted, then soft delete it.
     */
    public function delete(SupplierPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            if ($payment->ledgerTransaction?->status) {
                $this->supplierTransactionService->reverse($payment->ledgerTransaction);
            }

            if ($payment->purchase) {
                $payment->purchase->update([
                    'paid_amount' => max(0, $payment->purchase->paid_amount - $payment->bill_amount),
                    'updated_by' => Auth::id(),
                ]);
            }

            $this->post($payment, -1);
            $payment->delete();
        });
    }

    /**
     * Applies (direction 1) or reverses (direction -1) the payment's double
     * entry: credit the payment account with the total, debit Accounts
     * Payable with the bill amount and Bank Charges with the bank charge.
     */
    private function post(SupplierPayment $payment, int $direction): void
    {
        $this->ledgerPostingService->credit(ChartOfAccount::findOrFail($payment->payment_account_id), (float) $payment->total_amount * $direction);
        $this->ledgerPostingService->debit($this->predefinedAccount(LedgerPostingService::ACCOUNTS_PAYABLE_NUMBER), (float) $payment->bill_amount * $direction);

        if ((float) $payment->bank_charge > 0) {
            $this->ledgerPostingService->debit($this->predefinedAccount(LedgerPostingService::BANK_CHARGES_NUMBER), (float) $payment->bank_charge * $direction);
        }
    }

    /**
     * The request validates these accounts exist, so a missing one here is a bug.
     */
    private function predefinedAccount(string $accountNumber): ChartOfAccount
    {
        return $this->ledgerPostingService->predefinedAccount($accountNumber)
            ?? throw new \RuntimeException("Predefined account {$accountNumber} is missing.");
    }
}
