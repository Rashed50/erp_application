<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subcontractor Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        /* A4 Landscape Container */
        .a4-container {
            width: 297mm;
            min-height: 210mm;
            background: white;
            margin: 0 auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 999;
            opacity: 0.1;
            pointer-events: none;
            font-size: 48px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            letter-spacing: 3px;
        }

        /* Content Wrapper */
        .content {
            padding: 10px 15px 10px 15px;
            position: relative;
            z-index: 1;
            background: white;
        }

        /* Print Styles - CRITICAL FIXES */
        @media print {
            /* Remove all default body margins */
            body {
                background: white;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Remove default html margins */
            html {
                margin: 0 !important;
                padding: 0 !important;
            }

            .a4-container {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                z-index: 999;
                opacity: 0.12;
                pointer-events: none;
                font-size: 48px;
                font-weight: bold;
                color: #000;
                text-transform: uppercase;
                white-space: nowrap;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            /* Critical: Remove all default margins from page */
            @page {
                size: A4 landscape;
                margin: 0mm !important;
            }

            /* Remove any extra spacing from content */
            .content {
                padding: 10px 15px 10px 15px !important;
                margin: 0 !important;
            }

            /* Remove spacing from header */
            .header-table {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .header-table td {
                padding-top: 0 !important;
            }

            /* Prevent page breaks inside these elements */
            .header-table, .details-summary-table, .activity-table,
            .bottom-table, .signature-table, .disclaimer {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Ensure table rows don't break */
            .activity-table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Remove any browser-added spacing */
            .title-bar {
                margin-top: 0 !important;
            }
        }

        /* Screen Responsive */
        @media screen and (max-width: 300mm) {
            .a4-container {
                width: 100%;
                overflow-x: auto;
            }
            .content {
                min-width: 280mm;
            }
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

        /* Remove top spacing from first row of header */
        .header-table:first-child tr:first-child td {
            padding-top: 0;
        }

        .logo {
            height: 50px;
        }

        .title-bar {
            background-color: #003366;
            color: white;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 6px;
        }

        .info-table {
            margin-top: 10px;
            width: 35%;
            float: right;
        }

        .statement-info {
            text-align: left;
            font-size: 9px;
            padding: 4px;
            border: 1px solid #ccc;
        }

        .details-summary-table {
            margin-top: 10px;
            clear: both;
        }

        .section-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #003366;
        }

        .inner-table {
            width: 100%;
            border: none;
        }

        .inner-table td {
            padding: 2px 0;
            font-size: 9px;
            border: none;
        }

        .inner-table strong {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }

        .activity-table {
            margin-top: 12px;
            border: 1px solid #000;
        }

        .activity-table th {
            background-color: #d9b38c;
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        .activity-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9px;
        }

        .sub-total-row td {
            font-weight: bold;
            text-align: right;
            padding-right: 20px;
        }

        .bottom-table {
            margin-top: 15px;
            border: 1px solid #000;
        }

        .bottom-header {
            background-color: #003366;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            font-size: 10px;
        }

        .bottom-content td {
            padding: 6px;
            vertical-align: top;
            font-size: 9px;
            height: 60px;
            border: 1px solid #000;
        }

        .signature-table {
            margin-top: 20px;
        }

        .signature-table td {
            text-align: center;
            padding: 8px;
            width: 33%;
            font-size: 9px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            width: 160px;
            margin-left: auto;
            margin-right: auto;
        }

        .disclaimer {
            margin-top: 12px;
            font-size: 8px;
            border-top: 2px solid #003366;
            padding-top: 6px;
            text-align: justify;
            clear: both;
        }

        /* Clearfix */
        .clearfix {
            clear: both;
        }

        /* Balance row styling */
        .balance-row td {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="a4-container">
        <!-- Angled Watermark -->
        <div class="watermark">ASLOOB BEDAA CONTRACTING CO.</div>

        <div class="content">
            <table class="header-table">
                <tr>
                    <td style="text-align:left;font-size:10px; padding-top:0;">
                        <strong>Statement #:</strong> STMT-{{ $from_date }}-001<br>
                        <strong>Statement Period:</strong> {{ $from_date }} - {{ $to_date }}<br>
                        <strong>Print Date:</strong> {{ $current_datetime }}
                    </td>
                    <td style="text-align:right;">
                        @if(!empty($company->com_logo))
                            <img src="{{ asset($company->com_logo) }}" alt="Logo" width="180px" height="60px">
                        @endif
                    </td>
                </tr>
            </table>

            <table class="header-table">
                <tr>
                    <td class="title-bar">Subcontractor Statement</td>
                </tr>
            </table>

            <table class="details-summary-table">
                <tr>
                    <td width="45%" valign="top">
                        <div class="section-header">Subcontractor Details</div>
                        <table class="inner-table">
                            <tr>
                                <td><strong>Subcontractor Name:</strong></td>
                                <td>{{ $subcontractor_info->subcon_name ?? '' }}</td>
                            </tr>
                            {{-- <tr>
                                <td><strong>Ref. ID/PO:</strong></td>
                                <td></td>
                            </tr> --}}
                            <tr>
                                <td><strong>Iqama No:</strong></td>
                                <td>{{ $subcontractor_info->iqama_no ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone No:</strong></td>
                                <td>{{ $subcontractor_info->mobile_no ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email Address:</strong></td>
                                <td>{{ $subcontractor_info->subcon_name ?? '' }}@gmail.com</td>
                            </tr>
                        </table>
                    </td>
                    <td width="10%"></td>
                    <td width="45%" valign="top">
                        <div class="section-header" style="text-align: right;">Account Summary</div>
                        <table class="inner-table" style="text-align: right; float: right;">
                            {{-- <tr>
                                <td><strong>Account Type:</strong></td>
                                <td>Subcontractor</td>
                            </tr> --}}
                            <tr>
                                <td><strong>Opening Balance:</strong></td>
                                <td>SAR {{ number_format($subcontractor_info->upto_last_month_balance ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Invoice Amount:</strong></td>
                                <td>SAR {{ number_format($subcontractor_info->service_invoice_total_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Paid:</strong></td>
                                <td>SAR {{ number_format($subcontractor_info->total_paid_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Closing Balance:</strong></td>
                                <td>SAR {{ number_format(($subcontractor_info->service_invoice_total_amount ?? 0) - ($subcontractor_info->total_paid_amount ?? 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Net Payable:</strong></td>
                                <td>SAR {{ number_format(($subcontractor_info->upto_last_month_balance ?? 0) + ($subcontractor_info->service_invoice_total_amount ?? 0) - ($subcontractor_info->total_paid_amount ?? 0), 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 12px; font-weight: bold; font-size: 10px;">ACCOUNT ACTIVITY</div>

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
                        $balance = $subcontractor_info->upto_last_month_balance ?? 0;
                        $recordCount = 0;
                    @endphp

                    @if(isset($subcontractor_info->final_records) && count($subcontractor_info->final_records) > 0)
                        @foreach ($subcontractor_info->final_records as $record)
                            @php
                                $record['type'] == 'SERVICE' ? $balance += $record['amount'] : $balance -= $record['amount'];
                                $recordCount++;
                            @endphp
                            <tr>
                                <td>{{ $record['date'] ?? '' }}</td>
                                <td>{{ $record['type'] ?? '' }}</td>
                                <td>{{ $record['invoice_no'] ?? '' }}</td>
                                <td style="text-align:left">{{ $record['description'] ?? '' }}</td>
                                @if($record['type'] == 'SERVICE')
                                    <td>{{ number_format($record['amount'], 2) }}</td>
                                    <td>-</td>
                                @else
                                    <td>-</td>
                                    <td>{{ number_format($record['amount'], 2) }}</td>
                                @endif
                                <td>{{ number_format($balance, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <tr class="sub-total-row">
                        <td colspan="4" style="text-align: left;"><strong>Sub-Total</strong></td>
                        <td>{{ number_format($subcontractor_info->service_invoice_total_amount ?? 0, 2) }}</td>
                        <td>{{ number_format($subcontractor_info->total_paid_amount ?? 0, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="sub-total-row balance-row">
                        <td colspan="4" style="text-align: left;"><strong>Current Balance:</strong></td>
                        <td></td>
                        <td></td>
                        <td> {{ number_format($balance, 2) }} </td>
                    </tr>
                </tbody>
            </table>

            <table class="bottom-table">
                <tr>
                    <td class="bottom-header" width="33%">Payment Details</td>
                    <td class="bottom-header" width="33%">Subcontractor Acknowledgment</td>
                    <td class="bottom-header" width="33%">Subcontractor Sign/Thumb</td>
                </tr>
                <tr class="bottom-content">
                    <td>
                        <strong>Payment Method:</strong><br> <br><br>
                        {{-- <strong>Bank Name:</strong><br>
                        <strong>Account/IBAN Number:</strong> --}}
                    </td>
                    <td>I acknowledge receipt of this statement and agree with the details provided.</td>
                    <td></td>
                </tr>
            </table>

            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <strong>Prepared By:</strong><br>
                        {{ $login_name ?? '' }}
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <strong>Verified By:</strong><br>
                        Accounts Department
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <strong>Approved By:</strong><br>
                        CEO
                    </td>
                </tr>
            </table>

            <div class="disclaimer">
                <strong>DISCLAIMER:</strong> This statement is for information purposes only. Payment amounts, services, and dates may change based on project needs and contractor agreements. Please verify all details with Accounts Department (Asloob Bedaa Contracting Co.). For any other inquiries or discrepancies, please contact our Accounts Department at +96654181845, +96656024445 or email us at accounts@asloob.com
            </div>
        </div>
    </div>
</body>
</html>
