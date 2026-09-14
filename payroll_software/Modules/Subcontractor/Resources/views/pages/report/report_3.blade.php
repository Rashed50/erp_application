<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subcontractor Statement</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin-top: 0px;
            margin-bottom: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            padding-bottom: 5px;
            text-align: right;
        }
        .logo {
            height: 50px;
        }
        .title-bar {
            background-color: #003366;
            color: white;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 8px;
        }
        .info-table {
            margin-top: 10px;
            width: 30%;
            float: right;
        }
        .statement-info {
            text-align: left;
            font-size: 11px;
            padding: 5px;
        }
        .details-summary-table {
            margin-top: 15px;
            clear: both;
        }
        .section-header {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #003366;
        }
        .inner-table {
            width: 100%;
            border: none;
        }
        .inner-table td {
            padding: 4px 0;
            font-size: 11px;
            border: none;
        }
        .inner-table strong {
            display: inline-block;
            width: 140px;
            font-weight: bold;
        }
        .activity-table {
            margin-top: 15px;
            border: 1px solid #000;
        }
        .activity-table th {
            background-color: #d9b38c;
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
        .activity-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            height: 25px;
            font-size: 11px;
        }
        .sub-total-row td {
            font-weight: bold;
            text-align: right;
            padding-right: 20px;
        }
        .bottom-table {
            margin-top: 30px;
            border: 1px solid #000;
        }
        .bottom-header {
            background-color: #003366;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 5px;
        }
        .bottom-content td {
            padding: 8px;
            vertical-align: top;
            font-size: 11px;
            height: 80px;
            border: 1px solid #000;
        }
        .signature-table {
            margin-top: 40px;
        }
        .signature-table td {
            text-align: center;
            padding: 10px;
            width: 33%;
            font-size: 11px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .disclaimer {
            margin-top: 20px;
            font-size: 10px;
            border-top: 2px solid #003366;
            padding-top: 8px;
            text-align: justify;
            clear: both;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                {{-- <img src="{{ public_path('contents/admin/assets/images/logo_new_2.png') }}" class="logo" alt="Company Logo"> local server --}}
                 <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="title-bar">Subcontractor Statement</td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="statement-info">
                Statement #:STMT-2025-001 <br>
                Statement Period: {{ $month_name }} {{ $year }}<br>
                Print Date: {{ $current_datetime }}<br>
             </td>
        </tr>
    </table>

    <table class="details-summary-table">
        <tr>
            <td width="45%" valign="top">
                <div class="section-header">Subcontractor Details</div>
                <table class="inner-table">
                    <tr>
                        <td><strong>Subcontractor Name:</strong></td>
                        <td>{{ $subcontractor_info->subcon_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ref. ID/PO:</strong></td>
                        <td> </td>
                    </tr>
                    <tr>
                        <td><strong>Iqama No:</strong></td>
                        <td>{{ $subcontractor_info->iqama_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone No:</strong></td>
                        <td>{{ $subcontractor_info->mobile_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email Address:</strong></td>
                        <td>{{ $subcontractor_info->subcon_name }}@gmail.com</td>
                    </tr>
                </table>
            </td>
            <td width="10%"></td>
            <td width="45%" valign="top">
                <div class="section-header" style="text-align: right;">Account Summary</div>
                <table class="inner-table" style="text-align: right; float: right;">
                    <tr>
                        <td><strong>Account Type:</strong></td>
                        <td>Subcontractor</td>
                    </tr>
                    <tr>
                        <td><strong>Opening Balance:</strong></td>
                        <td>SAR {{ $subcontractor_info->upto_last_month_balance }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Invoice Amount:</strong></td>
                        <td>SAR {{ $subcontractor_info->service_invoice_total_amount }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Paid:</strong></td>
                        <td>SAR  {{ $subcontractor_info->total_paid_amount }}</td>
                    </tr>
                    <tr>
                        <td><strong>Closing Balance (if any):</strong></td>
                        <td>SAR {{$subcontractor_info->service_invoice_total_amount - $subcontractor_info->total_paid_amount }}</td>
                    </tr>
                    <tr>
                        <td><strong>Net Payable:</strong></td>
                        <td> SAR  {{ $subcontractor_info->upto_last_month_balance + $subcontractor_info->service_invoice_total_amount - $subcontractor_info->total_paid_amount }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; font-weight: bold; font-size: 12px;">ACCOUNT ACTIVITY</div>

    <table class="activity-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Invoice #</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $balance = $subcontractor_info->upto_last_month_balance;
            @endphp
            @foreach ($subcontractor_info->final_records as $record )

            @php
                $record['type'] == 'SERVICE' ? $balance += $record['amount']: $balance -= $record['amount'];
            @endphp

            <tr>
                <td>{{ $record['date'] }}</td>
                <td>{{ $record['type'] }}</td>
                <td>{{ $record['invoice_no'] }} </td>
                <td> {{ $record['description'] }}</td>
                @if($record['type'] == 'SERVICE')
                    <td> {{ $record['amount'] }}</td>
                    <td>-</td>
                @else
                    <td>-</td>
                    <td> {{ $record['amount'] }}</td>
                @endif
                <td>{{  $balance }}</td>
            </tr>



            @endforeach


            <tr class="sub-total-row">
                <td colspan="4" style="text-align: left;"><strong>Sub-Total</strong></td>
                <td>{{  $subcontractor_info->service_invoice_total_amount }} </td>
                <td> {{  $subcontractor_info->total_paid_amount }}</td>
                <td></td>
            </tr>
            <tr class="sub-total-row">
                <td colspan="4" style="text-align: left;"><strong>Current Balance:</strong></td>
                <td></td>
                <td></td>
                <td>{{  $balance }}</td>
            </tr>
        </tbody>
    </table>

    <table class="bottom-table">
        <tr>
            <td class="bottom-header" width="33%">Payment Details</td>
            <td class="bottom-header" width="33%">Subcontractor Acknowledgment:</td>
            <td class="bottom-header" width="33%">Subcontractor Sign/Thumb</td>
        </tr>
        <tr class="bottom-content">
            <td>
                <strong>Payment Method:</strong><br>
                <strong>Bank Name:</strong><br>
                <strong>Account/IBAN Number:</strong>
            </td>
            <td>I acknowledge receipt of this statement and agree with the details provided.</td>
            <td></td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                Prepared By:
                {{ $login_name }}
            </td>
            <td>
                <div class="signature-line"></div>
                Verify By : Accounts Department <br>
            </td>
            <td>
                <div class="signature-line"></div>
                Approved By: CEO <br>
            </td>
        </tr>
    </table>

    <div class="disclaimer">
        <strong>DISCLAIMER:</strong> This statement is for information purposes only. Payment amounts, services, and dates may change based on project needs and contractor agreements. Please verify all details with Accounts Department (Asloob Bedaa Contracting Co.) For any other inquiries or discrepancies, please contact our Accounts Department at +96654181845, +96656024445 or email us at accounts@asloob.com
    </div>

</body>
</html>
