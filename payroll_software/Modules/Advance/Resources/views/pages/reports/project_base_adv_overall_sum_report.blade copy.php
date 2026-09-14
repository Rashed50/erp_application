<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Advance Summary</title>
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
            top: 10%;
            left: 15%;
            opacity: 0.1;
            font-size: 120px;
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

        /* Background for negative balance */
        .bg-danger {
            background-color: #f8d7da !important; /* Light Red */
            fill: #f8d7da; /* For some SVG-based PDF engines */
            color: #721c24 !important;
            display: table-cell; /* Forces rendering in some engines */
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Table Styles */
        main {
            margin-top: 5px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 11px; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }

        .report-info-container {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 1px;
            padding-bottom: 1px;
        }

        .report-table-header {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
        }

        .report-table-header td {
            border: none !important;
            vertical-align: top;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="watermark">ASLOOB BEDAA CO.</div>

    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                   <img src="{{ asset($company->com_logo) }}" alt="Logo not found" width="210px" height="70px">
                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">{{ $company->comp_name_en }}</div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address }}</p>
                </td>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Printed: {{ $current_datetime }}</p>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div class="report-info-container">
            <table class="report-table-header">
                <tr>
                    <td style="width: 20%; text-align: left;">Project Name: {{ $projectName ?? '' }}</td>
                    <td style="width: 60%; text-align: center; font-size: 13px; font-weight: bold;">
                        Projectwise Deduction & Outstanding Balance Overview
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">S.N</th>
                    <th rowspan="2">ID</th>
                    <th rowspan="2">Employee Name</th>
                    <th rowspan="2">Iqama</th>
                    <th rowspan="2">Sponsor</th>
                    <th rowspan="2">Designation</th>
                    <th rowspan="2">BS/Rate</th>
                    <th rowspan="2">Closing <br> Balance <br>(B/F)</th>
                    <th colspan="3">Iqama Renewal Details</th>
                    <th colspan="3">Advance Details</th>
                    <th rowspan="2">Outstanding <br>Balance</th>
                </tr>
                <tr>
                    <th>Expense</th>
                    <th>Deducted</th>
                    <th>Balance</th>
                    <th>Taken</th>
                    <th>Recovered</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->employee_id }}</td>
                        <td>{{ $row->employee_name }}</td>
                        <td>{{ $row->akama_no }}</td>
                        <td>{{ $row->spons_name }}</td>
                        <td>{{ $row->catg_name }}</td>
                        <td class="text-right">{{ $row->basic_amount }}</td>
                        <td class="text-right">{{ $row->fiscal_year->balance_amount }}</td>
                        <td class="text-right">{{ number_format($row->iqama_renewal, 2) }}</td>
                        <td class="text-right">{{ number_format($row->iqama_deduction, 2) }}</td>
                        <td class="text-right">{{ number_format($row->iqama_balance, 2) }}</td>
                        <td class="text-right">{{ number_format($row->other_advance, 2) }}</td>
                        <td class="text-right">{{ number_format($row->other_advace_deduction, 2) }}</td>

                        <td class="text-right {{ (float)str_replace(',', '', $row->advance_balance) < 0 ? 'bg-danger' : '' }}">
    {{ number_format($row->advance_balance, 2) }}
</td>

                        <td class="text-right">{{ number_format($row->final_balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
