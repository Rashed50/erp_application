<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Cash Transaction Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 40px 60px 40px;

            @bottom-left {
                content: "Generated on: {{ $current_datetime }}";
                font-size: 8pt;
                color: #666;
            }

            @bottom-center {
                content: "System Generated";
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

        .top-hr {
            border: 0;
            border-top: 3px solid lightblue;
            margin: 0;
        }
        @page {
            size: A4 landscape;
            margin: 100px 40px 12px 40px; /* bottom reduced */
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ $company->comp_name_en ?? 'Company' }}</div>

    <!-- Header -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Generated on: {{ $current_datetime }}</p>
                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">
                        {{ $company->comp_name_en ?? 'Company Name' }}
                        {{-- <small>{{ $company->comp_name_arb }}</small> --}}
                    </div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address ?? 'Address' }}</p>
                </td>
                <td style="width: 20%; text-align: left;">
                     <img src="{{ asset($company->com_logo) }}"  alt="Not Found" width="210px" height="70px">
                </td>
            </tr>
        </table>

        <!-- Colorful divider line -->
        <hr class="top-hr">
    </header>

    <!-- Table -->
    <main>
        <div style="text-align:left; font-weight:bold; font-size:15px; margin-bottom:8px;">
            Cash Transaction Report from {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Ledger Name</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['ledger_name'] }}</td>
                    <td class="text-right">
                        {{ (!empty($item['debit']) && $item['debit'] != 0) ? number_format($item['debit'], 2) : '' }}
                    </td>
                    <td class="text-right">
                        {{ (!empty($item['credit']) && $item['credit'] != 0) ? number_format($item['credit'], 2) : '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th class="text-right">{{ number_format($totals['total_debit'], 2) }}</th>
                    <th class="text-right">{{ number_format($totals['total_credit'], 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </main>

    <!-- Footer -->
    <div class="footer" style="position:fixed; bottom:12px; left:0; right:0; text-align:center; font-size:10px; color:#666;">System Generated Statement Authorized by Accounts Department || <span class="footer-date">{{ $current_datetime }}</span></div>
</body>
</html>
