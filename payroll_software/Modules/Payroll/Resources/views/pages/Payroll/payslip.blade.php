<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee['name'] ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .payslip-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 40px 50px;
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .company-section {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .company-logo {

            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
            border: 1px solid #ddd;
        }

        .company-info h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-info .location {
            font-size: 13px;
            color: #7f8c8d;
        }

        .payslip-title-section {
            text-align: right;
        }

        .payslip-label {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 3px;
        }

        .payslip-month {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }

        /* Employee Summary Section */
        .summary-section {
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #5a6c7d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 30px;
        }

        .summary-left {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-right {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-left: 4px solid #4caf50;
            padding: 20px 25px;
            border-radius: 4px;
            min-width: 280px;
        }

        .net-pay-amount {
            font-size: 32px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 5px;
        }

        .net-pay-label {
            font-size: 13px;
            color: #558b2f;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            font-size: 14px;
        }

        .summary-label {
            min-width: 140px;
            color: #5a6c7d;
            position: relative;
        }

        .summary-label::after {
            content: ':';
            position: absolute;
            right: 0;
            margin-right: 10px;
        }

        .summary-value {
            color: #2c3e50;
            font-weight: 500;
            margin-left: 20px;
        }

        .days-info {
            display: flex;
            gap: 30px;
            padding-top: 10px;
            border-top: 1px dotted #c8e6c9;
        }

        .days-item {
            display: flex;
            align-items: baseline;
            gap: 10px;
            font-size: 13px;
        }

        .days-item::before {
            content: ':';
        }

        /* Additional Info Row */
        .additional-info {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 1px dotted #ddd;
            margin-bottom: 30px;
        }

        .info-group {
            display: flex;
            gap: 50px;
        }

        .info-item {
            display: flex;
            font-size: 14px;
        }

        .info-label {
            color: #5a6c7d;
            margin-right: 5px;
        }

        .info-label::after {
            content: ':';
            margin-left: 3px;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }

        /* Earnings and Deductions Table */
        .financial-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 35px;
            padding: 25px;
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }

        .column-header {
            font-size: 13px;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #bdbdbd;
        }

        .amount-header {
            text-align: right;
        }

        .financial-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 8px 0;
        }

        .item-name {
            color: #2c3e50;
        }

        .item-amount {
            color: #2c3e50;
            font-weight: 500;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
            padding: 12px 0;
            margin-top: 10px;
            border-top: 2px solid #bdbdbd;
        }

        /* Net Payable Section */
        .net-payable-section {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 6px;
            padding: 20px 25px;
            margin-bottom: 25px;
        }

        .net-payable-header {
            font-size: 14px;
            font-weight: 700;
            color: #2e7d32;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .net-payable-formula {
            font-size: 12px;
            color: #558b2f;
            margin-bottom: 12px;
        }

        .net-payable-value {
            font-size: 28px;
            font-weight: 700;
            color: #2e7d32;
            text-align: right;
        }

        /* Amount in Words */
        .amount-words {
            text-align: right;
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .amount-words strong {
            font-weight: 600;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #95a5a6;
            font-size: 12px;
            margin-top: 50px;
            padding-top: 20px;
        }

        .powered-by {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .zoho-logo {
            width: 18px;
            height: 18px;
            background-color: #e74c3c;
            border-radius: 3px;
        }

        .powered-text {
            color: #7f8c8d;
        }

        .footer-link {
            color: #3498db;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        @media print {
            body {
                padding: 0;
                background-color: white;
            }

            .payslip-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Header -->
        <div class="header">
            <div class="company-section">
                <div class="company-logo">
                     <img src="www.abccpayroll.com/uploads/abcc_logo_report.png"  alt="" width="210px" height="75px">
                </div>
                <div class="company-info">
                    <h1>{{ $company['name'] ?? 'Asloob Bedaa Co.' }}</h1>
                    <div class="location">{{ $company['location'] ?? 'Riyadh, Kingdom of Saudi Arabia' }}</div>
                </div>
            </div>
            <div class="payslip-title-section">
                <div class="payslip-label">Payslip For the Month</div>
                <div class="payslip-month">{{ $payroll['month'] ?? 'December 2025' }}</div>
            </div>
        </div>

        <!-- Employee Summary -->
        <div class="summary-section">
            <div class="section-title">EMPLOYEE SUMMARY</div>

            <div class="summary-grid">
                <div class="summary-left">
                    <div class="summary-item">
                        <span class="summary-label">Employee Name</span>
                        <span class="summary-value">{{ $employee['name'] ?? '' }} </span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Employee ID</span>
                        <span class="summary-value">{{ $employee['id'] ?? '' }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pay Period</span>
                        <span class="summary-value">{{ $payroll['period'] ?? '' }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pay Date</span>
                        <span class="summary-value">{{ $payroll['pay_date'] ?? '' }}</span>
                    </div>
                </div>

                <div class="summary-right">
                    <div class="net-pay-amount">{{ $payroll['currency'] ?? '₹' }}{{ number_format($payroll['net_pay'] ?? 0, 2) }}</div>
                    <div class="net-pay-label">Total Net Pay</div>
                    <div class="days-info">
                        <div class="days-item">
                            <span style="color: #558b2f;">Paid Days</span>
                            <strong style="color: #2e7d32;">{{ $payroll['paid_days'] ?? 0 }}</strong>
                        </div>
                        <div class="days-item">
                            <span style="color: #558b2f;">LOP Days</span>
                            <strong style="color: #2e7d32;">{{ $payroll['lop_days'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="additional-info">
            <div class="info-group">
                <div class="info-item">
                    <span class="info-label">Designation</span>
                    <span class="info-value">{{ $employee['designation'] ?? '' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Working Project</span>
                    <span class="info-value">{{ $employee['working_project'] ?? '' }} </span>
                </div>
            </div>
            <div class="info-group">
                <div class="info-item">
                    <span class="info-label">Bank Code</span>
                    <span class="info-value">{{ $employee['bank_code'] ?? '-' }}  </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Account No/IBN</span>
                    <span class="info-value">{{ $employee['account_no'] ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Financial Section -->
        <div class="financial-section">
            <div>
                <div style="display: grid; grid-template-columns: 1fr auto; margin-bottom: 15px;">
                    <div class="column-header">EARNINGS</div>
                    <div class="column-header amount-header">AMOUNT</div>
                </div>
                <div class="financial-items">
                    @foreach($earnings ?? [] as $earning)
                    <div class="financial-row">
                        <span class="item-name">{{ $earning['label'] }}</span>
                        <span class="item-amount">{{ $payroll['currency'] ?? '₹' }}{{ number_format($earning['amount'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="total-row">
                    <span>Gross Earnings</span>
                    <span>{{ $payroll['currency'] ?? '₹' }}{{ number_format($totals['gross_earnings'] ?? 0, 2) }}</span>
                </div>
            </div>

            <div>
                <div style="display: grid; grid-template-columns: 1fr auto; margin-bottom: 15px;">
                    <div class="column-header">DEDUCTIONS</div>
                    <div class="column-header amount-header">AMOUNT</div>
                </div>
                <div class="financial-items">
                    @foreach($deductions ?? [] as $deduction)
                    <div class="financial-row">
                        <span class="item-name">{{ $deduction['label'] }}</span>
                        <span class="item-amount">{{ $payroll['currency'] ?? '₹' }}{{ number_format($deduction['amount'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="total-row">
                    <span>Total Deductions</span>
                    <span>{{ $payroll['currency'] ?? '₹' }}{{ number_format($totals['total_deductions'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Payable -->
        <div class="net-payable-section">
            <div class="net-payable-header">TOTAL NET PAYABLE</div>
            <div class="net-payable-formula">Gross Earnings - Total Deductions</div>
            <div class="net-payable-value">{{ $payroll['currency'] ?? '₹' }}{{ number_format($totals['net_payable'] ?? 0, 2) }}</div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount In Words :</strong> {{ $payroll['amount_in_words'] ?? 'Indian Rupee Zero Only' }}
        </div>

        <!-- Footer Note -->
        <div style="text-align: center; color: #95a5a6; font-size: 12px; margin-bottom: 30px;">
            -- This is a system-generated document. --
        </div>

        <!-- Powered By Footer -->
        <div class="footer">
            <div class="powered-by">
                <span class="powered-text">Powered by</span>
                <div class="zoho-logo"></div>
                <span style="font-weight: 600; color: #2c3e50;">ABC Payroll</span>
            </div>
            <div>
                {{-- Simplify payroll and Compliance.  <a href="https://zoho.com/payroll" class="footer-link">zoho.com/payroll</a> --}}
            </div>
        </div>
    </div>
</body>
</html>
