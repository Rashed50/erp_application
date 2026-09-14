<?php

namespace Modules\Account\Http\Controllers;

use App\Models\AccountsModule\ChartOfAccounts;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Models\AccountTransactionDetails;




class AccountReportsController extends Controller
{
    

    public function generalLedger(Request $request)
    {
        return view('account::pages.reports.general_ledger');
    }




    public function generalLedgerPdf(Request $request)
    {




        $account = ChartOfAccounts::where('chart_of_acct_id', $request->account)->first();


        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ]);
        }

        // convert html date input to database date format
        $from_date = date('Y-m-d H:i:s', strtotime($request->from_date));
        // time is set to 23:59:59 to include all transactions of the end date
        $to_date = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));



        // opening balance
        $opening_debit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
            ->where('created_at', '<', $from_date)
            ->sum('debit');


        $opening_credit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
            ->where('created_at', '<', $from_date)
            ->sum('credit');

        $opening_balance = $opening_debit_balance - $opening_credit_balance;

        $opening_balance_type = $opening_balance > 0 ? 'Dr' : 'Cr';
        $opening_balance = abs($opening_balance);

        $transactions = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
            ->whereBetween('created_at', [$from_date, $to_date])->orderBy('created_at', 'asc')
            ->get()->toArray();

        // balance calculation
        $balance = $opening_balance;
        for ($i = 0; $i < count($transactions); $i++) {
            $balance += floatval($transactions[$i]['debit']) - floatval($transactions[$i]['credit']);
            $transactions[$i]['balance_type'] = $balance > 0 ? 'Dr' : 'Cr';
            $transactions[$i]['balance'] = abs($balance);
            $transactions[$i]['created_at'] = date('d-m-Y', strtotime($transactions[$i]['created_at']));
        }


        $pdf = Pdf::loadView('account::pages.reports.general_ledger_table', [
            'account' => $account,
            'transactions' => $transactions,
            'from' => $request->from_date,
            'to' => $request->to_date,
            'opening_balance' => $opening_balance,
            'opening_balance_type' => $opening_balance_type,
            'closing_balance' => $balance,
            'closing_balance_type' => $balance > 0 ? 'Dr' : 'Cr',
        ]);

        return $pdf->download('general_ledger.pdf');
    }

    public function trialBalance(Request $request)
    {
        return view('account::pages.reports.trial_balance');
    }

    public function trialBalancePdf(Request $request)
    {




        $request->validate([
            'from_date' => 'required|date|before:to_date',
            'to_date' => 'required|date|after:from_date',
            'report_type' => 'required|in:as_of_date,as_of_period',
        ]);


        $from_date = date('Y-m-d H:i:s', strtotime($request->from_date));
        $to_date = date('Y-m-d H:i:s', strtotime($request->to_date . ' 23:59:59'));


        $accounts = ChartOfAccounts::where('is_closed', 0)->get();

        if ($request->report_type == 'as_of_date') {

            $trial_balance = [];
            $total_debit = 0;
            $total_credit = 0;
            foreach ($accounts as $account) {
                $debit = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<=', $to_date)
                    ->sum('debit');

                $credit = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<=', $to_date)
                    ->sum('credit');

                $balance = $debit - $credit;
                $trial_balance[] = [
                    'account_no' => $account->chart_of_acct_number,
                    'account_name' => $account->chart_of_acct_name,
                    'debit' => max($balance, 0),
                    'credit' => $balance < 0 ? abs($balance) : 0,
                ];

                $total_debit += max($balance, 0);
                $total_credit += $balance < 0 ? abs($balance) : 0;
            }

            $pdf = Pdf::loadView('account::pages.reports.trial_balance_table', [
                'accounts' => $trial_balance,
                'from' => $request->from_date,
                'to' => $request->to_date,
                'total_debit' => $total_debit,
                'total_credit' => $total_credit,
            ]);

            return $pdf->download('trial_balance.pdf');
        } else {
            // as of period
            $opening_balance = [];
            $closing_balance = [];

            foreach ($accounts as $account) {
                $opening_debit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<', $from_date)
                    ->sum('debit');

                $opening_credit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<', $from_date)
                    ->sum('credit');

                $balance = $opening_debit_balance - $opening_credit_balance;

                $opening_balance[$account->chart_of_acct_id] = [
                    'debit' => max($balance, 0),
                    'credit' => $balance < 0 ? abs($balance) : 0,
                    'account_name' => $account->chart_of_acct_name,
                    'account_no' => $account->chart_of_acct_number,
                ];

                $closing_debit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<=', $to_date)
                    ->sum('debit');

                $closing_credit_balance = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->where('created_at', '<=', $to_date)
                    ->sum('credit');

                $balance = $closing_debit_balance - $closing_credit_balance;

                $closing_balance[$account->chart_of_acct_id] = [
                    'debit' => max($balance, 0),
                    'credit' => $balance < 0 ? abs($balance) : 0,
                    'account_name' => $account->chart_of_acct_name,
                    'account_no' => $account->chart_of_acct_number,
                ];
            }

            $current_balance = [];
            foreach ($accounts as $account) {
                $debit = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->sum('debit');

                $credit = AccountTransactionDetails::where('account_no', $account->chart_of_acct_id)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->sum('credit');

                $balance = $debit - $credit;

                $current_balance[$account->chart_of_acct_id] = [
                    'debit' => max($balance, 0),
                    'credit' => $balance < 0 ? abs($balance) : 0,
                    'account_name' => $account->chart_of_acct_name,
                    'account_no' => $account->chart_of_acct_number,
                ];
            }

            // merge opening balance, current balance and closing balance
            $trial_balance = [];
            $total_debit = 0;
            $total_credit = 0;

            foreach ($current_balance as $key => $value) {
                // check if account is not in opening balance
                $total_debit += $value['debit'];
                $total_credit += $value['credit'];

                $trial_balance[] = [
                    'account_no' => $value['account_no'],
                    'account_name' => $value['account_name'],
                    'opening_debit' => array_key_exists($key, $opening_balance) ? $opening_balance[$key]['debit'] : 0,
                    'opening_credit' => array_key_exists($key, $opening_balance) ? $opening_balance[$key]['credit'] : 0,
                    'debit' => $value['debit'],
                    'credit' => $value['credit'],
                    'closing_debit' => array_key_exists($key, $closing_balance) ? $closing_balance[$key]['debit'] : 0,
                    'closing_credit' => array_key_exists($key, $closing_balance) ? $closing_balance[$key]['credit'] : 0,
                ];
            }


            $pdf = Pdf::loadView('account::pages.reports.trial_balance_period_table', [
                'accounts' => $trial_balance,
                'from' => $request->from_date,
                'to' => $request->to_date,
                'total_debit' => $total_debit,
                'total_credit' => $total_credit,
            ]);

            return $pdf->download('trial_balance_period.pdf');

        }
    }

    public function calculateProfitLoss()
    {
        // Fetch total Revenue
        $totalRevenue = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Revenue');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        // Fetch total Expenses
        $totalExpense = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Expense');
            })
            ->selectRaw('SUM(debit - credit) as total')
            ->value('total');

        // Calculate profit or loss
        $profitOrLoss = $totalRevenue - $totalExpense;

        if ($profitOrLoss > 0) {
            $result = "Profit: " . number_format($profitOrLoss, 2);
        } else if ($profitOrLoss < 0) {
            $result = "Loss: " . number_format(abs($profitOrLoss), 2);
        } else {
            $result = "Break-Even: No profit, no loss.";
        }

        return view('account::pages.reports.profit_loss', compact('totalRevenue', 'totalExpense', 'result'));
    }



    public function downloadProfitLossReport()
    {
        // Fetch the same data used in the view
        $totalRevenue = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Revenue');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        $totalExpense = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Expense');
            })
            ->selectRaw('SUM(debit - credit) as total')
            ->value('total');

        $profitOrLoss = $totalRevenue - $totalExpense;

        if ($profitOrLoss > 0) {
            $result = "Profit: " . number_format($profitOrLoss, 2);
        } else if ($profitOrLoss < 0) {
            $result = "Loss: " . number_format(abs($profitOrLoss), 2);
        } else {
            $result = "Break-Even: No profit, no loss.";
        }

        // Pass profitOrLoss to the view as well
        $pdf = Pdf::loadView('account::pages.reports.purchase.profit_and_loss_pdf', compact('totalRevenue', 'totalExpense', 'result', 'profitOrLoss'));

        return $pdf->download('profit_loss_report.pdf');
    }



    public function generateBalanceSheet()
    {

        // Fetch total Assets
        $totalAssets = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Asset');
            })
            ->selectRaw('SUM(debit - credit) as total')
            ->value('total');

        // Fetch total Liabilities
        $totalLiabilities = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Liability');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        // Fetch total Owner's Equity
        $totalEquity = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Owner Equity');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        // Check that the balance sheet equation holds
        $isBalanced = ($totalAssets == ($totalLiabilities + $totalEquity));

        return view('account::pages.reports.balance_sheet', compact('totalAssets', 'totalLiabilities', 'totalEquity', 'isBalanced'));
    }



    public function downloadBalanceSheet()
    {
        // Fetch total assets
        $totalAssets = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Asset');
            })
            ->selectRaw('SUM(debit - credit) as total')
            ->value('total');

        // Fetch total liabilities
        $totalLiabilities = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Liability');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        // Fetch total equity
        $totalEquity = AccountTransactionDetails::join('chart_of_accounts', 'account_transaction_details.account_no', '=', 'chart_of_accounts.chart_of_acct_id')
            ->where('chart_of_accounts.acct_type_id', '=', function($query) {
                $query->select('acct_type_id')
                    ->from('chartof_account_types')
                    ->where('acct_type_name', 'Owner Equity');
            })
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');

        // Check if the balance sheet is balanced
        $isBalanced = ($totalAssets == ($totalLiabilities + $totalEquity));

        // Load the view for the PDF
        $pdf = Pdf::loadView('account::pages.reports.purchase.balance_sheet_pdf', compact('totalAssets', 'totalLiabilities', 'totalEquity', 'isBalanced'));

        // Download the PDF
        return $pdf->download('balance_sheet_report.pdf');
    }

}
