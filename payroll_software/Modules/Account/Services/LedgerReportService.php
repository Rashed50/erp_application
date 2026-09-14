<?php

namespace Modules\Account\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Account\Models\{ChartofaccSalesRecord,PurchaseInvoice};



class LedgerReportService{



    public function processSaleAndPurchaseDateByDateledgerReport(  $from_date,$to_date)
    {
        $fromDate = Carbon::parse($from_date)->startOfDay();
        $toDate   = Carbon::parse($to_date)->endOfDay();

        // Sales
        $sales = ChartofaccSalesRecord::query()
            ->whereBetween('sr_issue_date', [$fromDate, $toDate])
            ->where('is_draft', 0)
            ->where('sr_status', '!=', 'cancelled')
            ->get([
                'sr_auto_id',
                'sr_invoice_no',
                'sr_issue_date',
                'sr_invoice_description',
                'sr_grand_total_amount',
            ])
            ->map(function ($sale) {
                return [
                    'id'          => $sale->sr_auto_id,
                    'date'        => $sale->sr_issue_date,
                    'type'        => 'Sale',
                    'reference'   => $sale->sr_invoice_no,
                    'description' => $sale->sr_invoice_description,
                    'amount'      => (float) $sale->sr_grand_total_amount,

                    // Sales increase balance
                    'debit'       => 0,
                    'credit'      => (float) $sale->sr_grand_total_amount,
                ];
            });

        // Purchases
        $purchases = PurchaseInvoice::query()
            ->whereBetween('purchase_date', [$fromDate, $toDate])
            ->get([
                'pur_id',
                'invoice_number',
                'purchase_date',
                'description',
                'net_total',
            ])
            ->map(function ($purchase) {
                return [
                    'id'          => $purchase->pur_id,
                    'date'        => $purchase->purchase_date,
                    'type'        => 'Purchase',
                    'reference'   => $purchase->invoice_number,
                    'description' => $purchase->description,
                    'amount'      => (float) $purchase->net_total,
                    // Purchase decreases balance
                    'debit'       => (float) $purchase->net_total,
                    'credit'      => 0,
                ];
            });

        // Combine sales + purchases
        $ledger = $sales
            ->concat($purchases)
            ->sortBy([
                ['date', 'asc'],
                ['type', 'asc'],
            ])
            ->values();

        // Running balance
        $balance = 0;

        $ledger = $ledger->map(function ($transaction) use (&$balance) {

            $balance += $transaction['credit'];
            $balance -= $transaction['debit'];
            $transaction['balance'] = $balance;
            return $transaction;
        });

        return [
            'from_date' => $fromDate->toDateString(),
            'to_date'   => $toDate->toDateString(),
            'total_sales' => $sales->sum('credit'),
            'total_purchase' => $purchases->sum('debit'),
            'balance' => $balance,
            'ledger' => $ledger,
        ];
    }

 }
