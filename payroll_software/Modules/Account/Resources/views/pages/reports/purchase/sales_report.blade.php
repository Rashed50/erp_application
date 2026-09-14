<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 40px 60px 40px;

            @bottom-left {
                content: "Generated on: {{ $current_datetime }}";
                font-size: 8pt;
                color: #666;
            }

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 8pt;
                color: #666;
            }
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 30%;
            left: 15%;
            opacity: 0.05;
            font-size: 80px;
            transform: rotate(-30deg);
            z-index: -1;
        }

        /* Header */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none !important;
        }
        .logo {
            width: 140px;
            height: 55px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .company-name small {
            font-size: 14px;
            direction: rtl;
            display: block;
        }
        .label {
            font-weight: bold;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            padding: 3px 12px;
            display: inline-block;
            margin-top: 5px;
        }

        /* Table */
        main {
            margin-top: 5px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 11px; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        tfoot th {
            background: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">ASLOOB BEDAA CO.</div>

    <!-- Header -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}" alt="Company Logo" class="logo">
                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">
                        {{ $company->comp_name_en }}
                        {{-- <small>{{ $company->comp_name_arb }}</small> --}}
                    </div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address }}</p>
                </td>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Generated on: {{ $current_datetime }}</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- Table -->
    <main>
        <div style="text-align: left; font-weight: bold; font-size: 15px; margin-bottom: 2px;">
            Sales Report:
        </div>

        <table>
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Project</th>
                    <th>Due Date</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">VAT</th>
                    <th class="text-right">Grand Total</th>
                    <th class="text-right">Retention</th>
                    <th class="text-right">Receivable</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $row)
                    <tr>
                        <td>{{ $row->sr_invoice_no }}</td>
                        <td>{{ $row->ProjectDetails->proj_name ?? '-' }}</td>
                        <td>{{ $row->sr_due_date }}</td>
                        <td class="text-right">{{ number_format($row->sr_total_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($row->sr_vat_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($row->sr_grand_total_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($row->retention_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($row->sr_grand_total_amount - $row->retention_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Totals:</th>
                    <th class="text-right">{{ number_format($totals['total_amount'], 2) }}</th>
                    <th class="text-right">{{ number_format($totals['total_vat'], 2) }}</th>
                    <th class="text-right">{{ number_format($totals['total_grand'], 2) }}</th>
                    <th class="text-right">{{ number_format($totals['retention_amount'], 2) }}</th>
                    <th class="text-right">{{ number_format($totals['receivable_amount'], 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </main>
</body>
</html>
