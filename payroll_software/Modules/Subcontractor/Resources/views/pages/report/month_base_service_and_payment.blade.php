<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manpower Supplier Monthly Payment Records</title>
    <style>
        * {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: #e8e8e8;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            font-size: 12px;
            color: #1a1a1a;
        }

        /* ── A4 landscape wrapper ─────────────────────────── */
        .page-container {
            background-color: #ffffff;
            width: 297mm;
            min-height: 210mm;
            padding: 10mm 12mm 10mm 12mm;
            margin: 0 auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.25);
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                display: block;
            }
            @page {
                size: A4 landscape;
                margin: 10mm 12mm;
            }
            .page-container {
                width: 100%;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .no-print { display: none !important; }

            /* Force background colours when printing */
            #report-table th {
                background-color: #1B4F8A !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #report-table tr.row-alt {
                background-color: #D6E4F0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #report-table tfoot tr {
                background-color: #1B4F8A !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .date-badge {
                background-color: #FFD700 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .company-header {
                border-bottom: 1.5px solid #1B4F8A !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* ── Company header ──────────────────────────────── */
        .company-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #1B4F8A;
            padding-bottom: 6px;
        }
        .company-header td {
            border: none;
            vertical-align: middle;
            padding: 0 6px;
        }
        .company-logo {
            height: 60px;
            width: auto;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .company-address {
            font-size: 12px;
            color: #555;
            margin-top: 2px;
        }

        /* ── Report title ─────────────────────────────────── */
        .report-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: #1a1a1a;
        }

        /* ── Meta row (Month / Year / Date) ──────────────── */
        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .meta-left {
            display: flex;
            gap: 30px;
        }
        .meta-left span {
            font-weight: 600;
        }
        .date-badge {
            background-color: #FFD700;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 2px;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        /* ── Main table ───────────────────────────────────── */
        #report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        #report-table th {
            background-color: #1B4F8A;
            color: #ffffff;
            font-weight: 600;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #14407a;
            white-space: nowrap;
        }

        #report-table td {
            border: 1px solid #b0c8e0;
            padding: 5px 4px;
            vertical-align: middle;
        }

        #report-table tbody tr {
            background-color: #ffffff;
        }
        #report-table tbody tr.row-alt {
            background-color: #D6E4F0;
        }

        #report-table tfoot tr {
            background-color: #1B4F8A;
            color: #ffffff;
            font-weight: bold;
        }
        #report-table tfoot td {
            border: 1px solid #14407a;
            padding: 5px 4px;
        }

        /* Column alignment helpers */
        .col-center { text-align: center; }
        .col-right  { text-align: right;  padding-right: 6px; }
        .col-left   { text-align: left;   padding-left: 6px; }

        /* ── Signature row ────────────────────────────────── */
        /* .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }
        .sig-line {
            display: block;
            border-top: 1px solid #333;
            margin-bottom: 4px;
            margin-top: 30px;
        }
        .sig-label {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        } */

          .signature-table {
            width: 100%;
            margin-top: 60px;
        }

        /* ── Print button ─────────────────────────────────── */
        .print-btn {
            display: block;
            margin: 12px auto 0;
            background-color: #1B4F8A;
            color: #ffffff;
            border: none;
            padding: 7px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .print-btn:hover { background-color: #14407a; }
    </style>
</head>
<body>
<div class="page-container">

    {{-- ── Company header ─────────────────────────────────── --}}
    <table class="company-header">
        <tr>
            <td style="width:20%; text-align:left;">
                <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
                     alt="Logo" class="company-logo">
            </td>
            <td style="width:60%; text-align:center;">
                <div class="company-name">{{ $company->comp_name_en }}</div>
                <div class="company-address">{{ $company->comp_address }}</div>
            </td>
            <td style="width:20%; text-align:right;">
                <button class="print-btn no-print" onclick="window.print()">PRINT</button>
            </td>
        </tr>
    </table>

    {{-- ── Title ───────────────────────────────────────────── --}}
    <div class="report-title">Manpower Supplier Monthly Payment Records</div>

    {{-- ── Meta row ─────────────────────────────────────────── --}}
    <div class="meta-row">
        <div class="meta-left">
            <div>Month: <span>{{ $month_name }}</span></div>
            <div>Year: <span>{{ $year }}</span></div>
        </div>
        <div class="date-badge">{{ $print_date }}</div>
    </div>

    {{-- ── Main table ───────────────────────────────────────── --}}
    @php
        $grand_invoice  = 0;
        $grand_paid     = 0;
        $grand_prev_bal = 0;
        $grand_adv_adj  = 0;
        $grand_net      = 0;
    @endphp

    <table id="report-table">
        <thead>
            <tr>
                <th style="width:3%">S.N</th>
                <th style="width:14%">Supplier Name</th>
                <th style="width:9%">Invoice No.</th>
                <th style="width:8%">Invoice <br> Date</th>
                <th style="width:10%">Invoice <br> Amount (SAR)</th>
                <th style="width:9%">Paid This <br> Month (SAR)</th>
                <th style="width:9%">Previous <br>Balance (SAR)</th>
                <th style="width:9%">Advance <br>Adjustment (SAR)</th>
                <th style="width:9%">Net <br>Payable (SAR)</th>
                <th style="width:8%">Payment <br> Method</th>
                <th style="width:12%">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $row)
                @php
                    $grand_invoice  += $row->total_invoice;
                    $grand_paid     += $row->paid_this_month;
                    $grand_prev_bal += $row->prev_balance;
                    $grand_adv_adj  += $row->advance_adj;
                    $grand_net      += $row->net_payable;

                    $method_text = match((string)($row->payment_method ?? '')) {
                        '1'     => 'Cash',
                        '2'     => 'Bank Transfer',
                        default => '-',
                    };
                @endphp
                <tr class="{{ $loop->even ? 'row-alt' : '' }}">
                    <td class="col-center">{{ $loop->iteration }}</td>
                    <td class="col-left">{{ $row->subcon_name }}</td>
                    <td class="col-center">{{ $row->invoice_no }}</td>
                    <td class="col-center">
                        {{ $row->invoice_date && $row->invoice_date !== '-'
                            ? \Carbon\Carbon::parse($row->invoice_date)->format('d-m-Y')
                            : '-' }}
                    </td>
                    <td class="col-right">{{ number_format($row->total_invoice, 2) }}</td>
                    <td class="col-right">{{ $row->paid_this_month > 0 ? number_format($row->paid_this_month, 2) : '-' }}</td>
                    <td class="col-right">{{ $row->prev_balance != 0 ? number_format($row->prev_balance, 2) : '-' }}</td>
                    <td class="col-right">{{ $row->advance_adj > 0 ? number_format($row->advance_adj, 2) : '-' }}</td>
                    <td class="col-right" style="{{ $row->net_payable > 0 ? 'font-weight:600;' : 'color:#c00;' }}">
                        {{ number_format($row->net_payable, 2) }}
                    </td>
                    <td class="col-center">{{ $method_text }}</td>
                    <td class="col-left">{{ $row->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="col-center" style="padding:20px; color:#666;">
                        No records found for {{ $month_name }} {{ $year }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($records) > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="col-right">TOTAL</td>
                <td class="col-right">{{ number_format($grand_invoice, 2) }}</td>
                <td class="col-right">{{ number_format($grand_paid, 2) }}</td>
                <td class="col-right">{{ number_format($grand_prev_bal, 2) }}</td>
                <td class="col-right">{{ number_format($grand_adv_adj, 2) }}</td>
                <td class="col-right">{{ number_format($grand_net, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- ── Signature row ──────────────────────────────────── --}}
        <table class="signature-table" style="border: none;">
                <tr style="border: none;">
                    <td style="width: 33%; text-align: left; border: none;">
                        {{-- <p style="margin-bottom: 40px;"><b>----------------------</b></p> --}}
                        <p><b>{{ $login_name }}</b><br>Prepared By</p>
                    </td>
                     <td style="width: 33%; text-align: center; border: none;">
                        {{-- <p style="margin-bottom: 40px;"><b>----------------------</b></p> --}}
                        <p> <br>Verified By</p>
                    </td>
                    <td style="width: 33%; text-align: right; border: none;">
                        {{-- <p style="margin-bottom: 40px;"><b>-----------------------------</b></p> --}}
                        <p><b>Authorized Signature</b></p>
                    </td>
                </tr>
        </table>


</div>
</body>
</html>
