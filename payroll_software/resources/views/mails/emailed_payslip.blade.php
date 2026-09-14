<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->employee_name ?? '' }}</title>
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 40px 20px 20px;  /* Increased right padding */
        }
        .payslip {
            max-width: 750px;
            width: 100%;
            background: #ffffff;
            padding: 30px 40px 30px 25px;  /* Increased right padding */
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            position: relative;
            overflow: hidden;
            margin-right: 10px;  /* Added right margin */
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            font-weight: 700;
            color: rgba(200, 200, 200, 0.15);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 5px;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Header - Single row with flex */
       .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: left;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: right;
            padding-right: 5px;
        }

        .header-left .year {
            font-size: 14px;
            color: #2c3e50;
            font-weight: 600;
        }

        .header-left .title {
            font-size: 20px;
            font-weight: 700;
            color: #1a2a3a;
            letter-spacing: 2px;
            margin-left: 15px;
        }

        .header-right .company-name {
            font-size: 15px;
            font-weight: 700;
            color: #1a2a3a;
        }

        .header-right .company-address {
            font-size: 11px;
            color: #555;
            line-height: 1.4;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .info-table td {
            padding: 6px 10px;
            border: 1px solid #d0d0d0;
        }
        .info-table .label {
            font-weight: 600;
            background: #f5f6f8;
            width: 22%;
        }
        .info-table .value {
            width: 28%;
        }

        /* Attendance Table */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #d0d0d0;
            padding: 8px 12px;
            text-align: center;
        }
        .attendance-table th {
            background: #f1f3f5;
            font-weight: 600;
        }
        .attendance-table .title-row td {
            background: #e9ecef;
            font-weight: 700;
            text-align: center;
        }

        /* Combined Earnings & Deductions Table */
        .combined-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }
        .combined-table td {
            border: 1px solid #d0d0d0;
            padding: 6px 12px;
        }
        .combined-table .title-row td {
            background: #e9ecef;
            font-weight: 700;
            text-align: center;
        }
        .combined-table .label-cell {
            font-weight: 500;
        }
        .combined-table .amount-cell {
            text-align: right;
            font-weight: 500;
            padding-right: 15px;
        }
        .combined-table .total-row td {
            font-weight: 700;
            background: #f8f9fa;
        }
        .combined-table .earnings-col {
            width: 30%;
        }
        .combined-table .amount-col {
            width: 20%;
        }
        .combined-table .deductions-col {
            width: 30%;
        }
        .combined-table .deductions-amount-col {
            width: 20%;
        }

        /* Net Salary Table */
        .net-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .net-table td {
            border: 1px solid #d0d0d0;
            padding: 8px 12px;
        }
        .net-table .net-label {
            font-weight: 700;
            background: #f8f9fa;
            width: 25%;
        }
        .net-table .net-amount {
            font-weight: 700;
            text-align: right;
            padding-right: 15px;
        }
        .net-table .payable-row td {
            font-weight: 700;
            font-size: 16px;
            background: #d4edda;
        }
        .net-table .payable-row .net-amount {
            font-size: 18px;
            color: #1a5e2a;
        }

        /* Net Pay in Words */
        .net-words {
            margin-top: 20px;
            padding: 12px 0 8px 0;
            border-top: 2px solid #2c3e50;
            font-weight: 500;
            font-size: 14px;
            color: #1a2a3a;
            position: relative;
            z-index: 1;
        }

        /* Signatures - Fixed to one row */
        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }
        .signatures .signature-item {
            text-align: center;
            flex: 1;
        }
        .signatures .line {
            display: block;
            width: 80%;
            max-width: 160px;
            border-top: 1px solid #333;
            margin: 30px auto 5px auto;
        }
        .signatures .label-text {
            font-size: 12px;
            color: #555;
            margin-top: 2px;
        }

        /* Utility */
        .text-center { text-align: center; }
        .mt-10 { margin-top: 10px; }
        .mb-10 { margin-bottom: 10px; }
        .fw-bold { font-weight: 700; }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .payslip {
                box-shadow: none !important;
                border: none !important;
                padding: 20px 35px 20px 20px !important;
                max-width: 100% !important;
                margin-right: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .watermark {
                color: rgba(200, 200, 200, 0.2) !important;
            }
        }
    </style>
</head>
<body>

<div class="payslip">

    {{-- WATERMARK --}}
    <div class="watermark">Asloob Bedaa Co.</div>

    {{-- HEADER - SINGLE ROW --}}
    <div class="header">
        <div class="header-left">
            {{-- <span class="year">2020 - 2021</span>
            <span class="title">PAYSLIP</span> --}}
        </div>

        <div class="header-right">
            <div class="company-name">Asloob Bedaa Co.</div>
            <div class="company-address">P.O. Box 12345, Riyadh, Saudi Arabia</div>
            <div class="company-address">Tel: +966 11 234 5678</div>
        </div>
    </div>


    {{-- EMPLOYEE INFO TABLE --}}
    <table class="info-table">
        <tr>
            <td class="label">Employee ID</td>
            <td class="value">{{ $salary_record->employee_id ?? ' ' }}</td>
            <td class="label">Payroll Month</td>
            <td class="value">{{ $salary_record->month ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Employee Name</td>
            <td class="value">{{ $salary_record->employee_name ?? '' }}</td>
            <td class="label">Department</td>
            <td class="value">{{ $salary_record->department ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Designation</td>
            <td class="value">{{ $salary_record->designation ?? '' }}</td>
            <td class="label">Date of Joining</td>
            <td class="value">{{ $salary_record->joining_date ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Payment Status </td>
             <td>{{ $salary_record->Status == 1 ? "Paid": Unpaid}}</td>
            {{-- <td class="value">{{ $salary_record->slh_paid_method == 1 ? 'Bank Transfer' :"Bank Transfer"}}</td> --}}
            <td class="label">Project / Cost Center</td>
            <td class="value">{{ $salary_record->working_project ?? '—' }}</td>
        </tr>
    </table>


    {{-- ATTENDANCE SUMMARY --}}
    <table class="attendance-table">
        <tr class="title-row">
            <td colspan="5">ATTENDANCE SUMMARY</td>
        </tr>
        <tr>
            <th>Calendar Days</th>
            <th>Paid Days</th>
            <th>Present</th>
            <th>Weekly Off</th>
            <th>Leave / LWP</th>
        </tr>
        <tr>
            <td>{{ $salary_record->calendar_days ?? 31 }}</td>
            <td>{{ $salary_record->paid_days ?? 31 }}</td>
            {{-- <td>{{ $salary_record->present_days ?? 27 }}</td> --}}
            <td>{{ $salary_record->weekly_off ?? 4 }}</td>
            <td>{{ $salary_record->lop_days ?? '0 / 0' }}</td>
        </tr>
    </table>



    {{-- COMBINED EARNINGS & DEDUCTIONS TABLE --}}
    <table class="combined-table">
        <tr class="title-row">
            <td class="earnings-col">EARNINGS &amp; ALLOWANCES</td>
            <td class="amount-col">AMOUNT</td>
            <td class="deductions-col">DEDUCTIONS</td>
            <td class="deductions-amount-col">AMOUNT</td>
        </tr>
        <tr>
            <td class="label-cell">Basic Salary</td>
            <td class="amount-cell">{{ number_format($salary_record->basic_amount ?? 0, 0, '.', ',') }}</td>
            <td class="label-cell">Salary Advance</td>
            <td class="amount-cell">{{ number_format($salary_record->partial_paid_amount ?? 0, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <td class="label-cell">Housing Allowance</td>
            <td class="amount-cell">{{ number_format($salary_record->house_rent ?? 0, 0, '.', ',') }}</td>
            <td class="label-cell">Other Deduction</td>
            <td class="amount-cell">{{ number_format($salary_record->other_deduction ?? 0, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <td class="label-cell">Other Allowance </td>
            <td class="amount-cell">{{ number_format(($salary_record->medical_allowance + $salary_record->local_travel_allowance + $salary_record->others ) ?? 0, 0, '.', ',') }}</td>
            <td class="label-cell">Iqama Renewal</td>
            <td class="amount-cell">{{ number_format($salary_record->slh_iqama_advance ?? 0, 0, '.', ',') }}</td>
        </tr>
        <tr>
            <td class="label-cell">Food Allowance</td>
            <td class="amount-cell">{{ number_format($salary_record->food_allowance ?? 0, 0, '.', ',') }}</td>
            <td class="label-cell"></td>
            <td class="amount-cell"></td>
        </tr>
        <tr>
            <td class="label-cell">Overtime</td>
            <td class="amount-cell">{{ number_format($salary_record->overtime_amount ?? 0, 0, '.', ',') }}</td>
            <td class="label-cell"></td>
            <td class="amount-cell"></td>
        </tr>
        <tr class="total-row">
            <td>TOTAL EARNINGS</td>
            <td class="amount-cell">{{ number_format($salary_record->slh_all_include_amount ?? 0, 0, '.', ',') }}</td>
            <td>TOTAL DEDUCTIONS</td>
            <td class="amount-cell">{{ number_format( ($salary_record->slh_all_include_amount - $salary_record->slh_total_salary) ?? 0, 0, '.', ',') }}</td>
        </tr>
    </table>

    {{-- NET SALARY PAYABLE --}}
    <table class="net-table">
        <tr class="payable-row">
            <td colspan="2" style="text-align: right; font-size: 16px;">NET SALARY PAYABLE</td>
            <td colspan="2" class="net-amount" style="font-size: 18px; color: #1a5e2a;">
                {{ number_format($salary_record->slh_total_salary ?? 0, 0, '.', ',') }}
            </td>
        </tr>
    </table>

    {{-- NET PAY IN WORDS --}}
    <div class="net-words">
        <strong>Net Pay in Words:</strong>
        {{ $salary_record->amount_in_words ?? 'SAR ... Only.' }}
    </div>

</div>

</body>
</html>
