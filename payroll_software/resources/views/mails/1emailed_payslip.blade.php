<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* PDF General Settings */
        @page { margin: 20px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        /* Layout Tables */
        .w-full { width: 100%; }
        table { border-collapse: collapse; width: 100%; }
        td { vertical-align: top; }

        /* Header */
        .company-name { font-size: 20px; font-weight: bold; color: #1a3a5a; }
        .company-info { font-size: 11px; color: #666; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Summary Section */
        .summary-container { margin-top: 20px; }
        .label { color: #666; width: 120px; }
        .value { font-weight: bold; }

        /* Net Pay Highlight Box */
        .net-pay-box {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .big-amount { font-size: 28px; font-weight: bold; color: #166534; margin: 0; }
        .net-pay-label { color: #15803d; font-weight: bold; font-size: 14px; }

        /* Tables for Earnings/Deductions */
        .data-table { margin-top: 20px; }
        .data-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
        }
        .bg-light { background-color: #fcfcfc; }
        .font-bold { font-weight: bold; }

        /* Bottom Totals */
        .total-banner {
            background-color: #f0fdf4;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <div class="container">
        <table class="w-full">
            <tr>
                <td>
                    <div class="company-name">Asloob Bedaa Contracting Company</div>
                    <div class="company-info">Riyadh, Kingdom of Saudi Arabia</div>
                </td>
                <td class="text-right">
                    <div style="color: #777;">Payslip For the Month</div>
                    <div style="font-size: 16px; font-weight: bold;">--- ---</div>
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

        <table class="summary-container w-full">
            <tr>
                <td width="60%">
                    <div style="font-weight: bold; color: #555; margin-bottom: 10px;">EMPLOYEE SUMMARY</div>
                    <table>
                        <tr><td>Employee Name</td><td>: <strong>{{ $employee['name'] }}</strong></td></tr>
                         <tr><td>Employee ID</td><td>: <strong>{{ $employee['id'] }}</strong></td></tr>
                        <tr><td>Paid Period</td><td>: {{ $payroll['period'] }}</td></tr>
                        <tr><td>Paid At</td><td>: {{ $payroll['pay_date'] ?? 'N/A' }}</td></tr>
                    </table>
                </td>
                <td width="40%">
                    <div class="net-pay-box">
                        <div class="big-amount">{{ $payroll['currency'] ?? 'SAR' }}{{ number_format($payroll['net_pay'] ?? 0, 2) }}</div>
                        <div class="net-pay-label">Total Net Pay</div>
                        <div style="margin-top: 10px; border-top: 1px dashed #bbf7d0; padding-top: 10px; font-size: 11px;">
                            Paid Days: {{ $payroll['paid_days'] ?? 0 }} <br> LOP Days:{{ $payroll['lop_days'] ?? 0 }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>


        <hr>
        <table class="w-full" style="margin-top: 15px; font-size: 11px;">
            <tr>
                <td  class="text-Left">Designation </td>
                <td>:</td>
                <td><strong> {{ $employee['designation'] ?? '' }} </strong></td>
                <td class="text-right">Working Project</td>
                <td>:</td>
                <td  class="text-Left"><strong> {{ $employee['working_project'] ?? '' }} </strong></td>
            </tr>
            <tr>

                <td class="text-Left">Bank Code</td>
                <td>:</td>
                <td  class="text-Left"><strong>{{ $employee['bank_code'] ?? '-' }}  </strong></td>
                <td class="text-right">Account No/IBN</td>
                <td>:</td>
                <td  class="text-Left"><strong >{{ $employee['account_no'] ?? '-' }}  </strong></td>
            </tr>
        </table>

        <table class="data-table w-full">
            <thead>
                <tr>
                    <th width="30%">EARNINGS</th>
                    <th width="20%">AMOUNT</th>
                    <th width="30%">DEDUCTIONS</th>
                    <th width="20%">AMOUNT</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>Basic</td>
                    <td class="text-right">0.00</td>
                    <td>Income Tax</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>HRA</td>
                    <td class="text-right">0.00</td>
                    <td>Provident Fund</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>Allowances</td>
                    <td class="text-right">0.00</td>
                    <td>Other</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr class="bg-light font-bold">
                    <td>Gross Earnings</td>
                    <td class="text-right"> {{ $payroll['currency'] ?? 'SAR ' }}{{ number_format($totals['gross_earnings'] ?? 0, 2) }} </td>
                    <td>Total Deductions</td>
                    <td class="text-right">{{ $payroll['currency'] ?? 'SAR ' }}{{ number_format($totals['total_deductions'] ?? 0, 2) }}</td>
                </tr>


            </tbody>
        </table>

        <div class="total-banner">
            <table class="w-full">
                <tr>
                    <td>
                        <div style="font-weight: bold;">TOTAL NET PAYABLE</div>
                        <div style="font-size: 10px; color: #666;">Gross Earnings - Total Deductions</div>
                    </td>
                    <td class="text-right" style="font-size: 20px; font-weight: bold;">
                         {{ $payroll['currency'] ?? 'SAR ' }}{{ number_format($totals['net_payable'] ?? 0, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 10px; text-align: right; font-size: 11px;">
            Amount In Words: <strong> {{ $payroll['amount_in_words'] ?? "" }} </strong>
        </div>

        <div class="footer-note">
            -- This is a system-generated document and does not require a signature. --
        </div>
    </div>

</body>
</html>
