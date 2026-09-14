<?php

namespace Modules\Employee\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmployeeController extends Controller
{
    public function create()
    {
        return view('employee::pages.employee.create', [
            'data'=>[

            ],
        ]);
    }

    public function payslip()
    {
        // Dummy data for testing
        $employee = [
            'name' => 'John Doe',
            'id' => 'EMP001',
            'designation' => 'Software Engineer',
            'working_project' => 'Payroll System',
            'bank_code' => 'SBIN0001234',
            'account_no' => '123456789012'
        ];

        $company = [
            'name' => 'Asloob Bedaa Contracting Company',
            'location' => 'Riyadh, Kingdom of Saudi Arabia'
        ];

        $payroll = [
            'month' => 'December 2025',
            'period' => '01-Dec-2025 to 31-Dec-2025',
            'pay_date' => '01-Jan-2026',
            'currency' => '₹',
            'net_pay' => 50000.00,
            'paid_days' => 30,
            'lop_days' => 0,
            'amount_in_words' => 'Indian Rupee Fifty Thousand Only'
        ];

        $earnings = [
            ['label' => 'Basic Salary', 'amount' => 30000.00],
            ['label' => 'HRA', 'amount' => 9000.00],
            ['label' => 'Conveyance Allowance', 'amount' => 1920.00],
            ['label' => 'Medical Allowance', 'amount' => 1250.00],
            ['label' => 'LTA', 'amount' => 2000.00]
        ];

        $deductions = [
            ['label' => 'Provident Fund', 'amount' => 3600.00],
            ['label' => 'Professional Tax', 'amount' => 235.00],
            ['label' => 'Income Tax', 'amount' => 4500.00]
        ];

        $totals = [
            'gross_earnings' => array_sum(array_column($earnings, 'amount')),
            'total_deductions' => array_sum(array_column($deductions, 'amount')),
            'net_payable' => array_sum(array_column($earnings, 'amount')) - array_sum(array_column($deductions, 'amount'))
        ];

        return view('employee::pages.employee.payslip', compact('employee', 'company', 'payroll', 'earnings', 'deductions', 'totals'));
    }
}
