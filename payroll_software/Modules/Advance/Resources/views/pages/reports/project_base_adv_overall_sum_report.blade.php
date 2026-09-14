<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Advance Summary</title>
    <style>
        /* 1. GLOBAL SETTINGS */
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            font-size: 12px;
            color: #333;
        }

        /* Landscape A4 Wrapper */
        .page-container {
            background-color: white;
            width: 297mm;
            min-height: 210mm;
            padding-left: 12mm;
            padding-right: 7mm;
            padding-top: 10mm;
            padding-bottom: 20mm; /* Space for disclaimer */
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
        }

        /* PRINT STYLES */
        @media print {
            /* body { background-color: white; padding: 0; display: block; }
            @page { size: A4 landscape; margin: 0; }
            .page-container { width: 100%; box-shadow: none; margin: 0; }
            .no-print { display: none !important; }
            .bg-danger {
                background-color: #f8d7da !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            } */

            body { background-color: white; padding: 0; display: block; }
            @page {
                size: A4 landscape;
                margin: 5mm 5mm 5mm 10mm; /* top right bottom left This creates the margin gap on every page */
            }
            .page-container { width: 100%; box-shadow: none; margin: 0; padding-bottom: 10mm; }
            .no-print { display: none !important; }

            /* Ensures header repeats on every page */
            thead { display: table-header-group; }

            /* Keep signature block together */
            .signature-section { page-break-inside: avoid; }

            .bg-danger {
                background-color: #f8d7da !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* HEADER & TABLES */
        header {
            width: 100%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .header-table, .report-table-header {
            width: 100%;
            border: none !important;
        }

        .header-table td, .report-table-header td {
            border: none !important;
            vertical-align: middle;
        }

        #summary-table {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
        }

        #summary-table th, #summary-table td {
            border: 0.5px solid #333;
            padding: 5px;
            font-size: 12px;
        }

        #summary-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            margin-top: 12px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Conditional Styling */
        .bg-danger { background-color: #f8d7da; color: #721c24; }

        .print__button {
            background: #000;
            color: #fff;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
        }

        /* SIGNATURE SECTION */
        .signature-section {
            margin-top: 40px;
            width: 100%;
            z-index: 1;
            position: relative;
        }
        .signature-table {
            width: 100%;
            margin-top: 60px;
        }
        .signature-table td {
            width: 33%;
            text-align: center;
            border: none !important;
        }
        .sig-line {
            border-top: 1px solid #000;
            width: 50%;
            margin: 0 auto 5px auto;
        }

        /* BOTTOM DISCLAIMER */
        .disclaimer {
            position: absolute;
            bottom: 8mm;
            left: 12mm;
            right: 7mm;
            font-size: 9px;
            color: #777;
            border-top: 0.5px solid lightgrey;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="page-container">
        <div class="watermark">ASLOOB BEDAA CO.</div>

        <header>
            <table class="header-table">
                <tr>
                    <td style="width: 25%; text-align: left;">
                        <img src="{{ asset($company->com_logo) }}" alt="Logo" style="height: 60px;">
                    </td>
                    <td style="width: 50%; text-align: center;">
                        <div style="font-size: 18px; font-weight: bold;">{{ $company->comp_name_en }}</div>
                        <div style="font-size: 10px;">{{ $company->comp_address }}</div>
                    </td>
                    <td style="width: 25%; text-align: right; font-size: 10px;">
                        Printed: {{ $current_datetime }} <br><br>
                        <button onclick="window.print()" class="print__button no-print">PRINT REPORT</button>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <table class="report-table-header">
                <tr>
                    <td style="width: 30%; font-weight: bold;">Project: {{ $projectName ?? 'N/A' }}</td>
                    <td style="width: 40%; text-align: center; font-size: 14px; font-weight: bold; padding-bottom: 10px">
                        Projectwise Deduction & Outstanding Balance Overview
                    </td>
                    <td style="width: 30%; text-align: right;"></td>
                </tr>
            </table>

            <table id="summary-table">
                <thead>
                    <tr>
                        <th rowspan="2">S.N</th>
                        <th rowspan="2">ID</th>
                        <th rowspan="2">Employee Name</th>
                        <th rowspan="2">Iqama</th>
                        <th rowspan="2">Sponsor</th>
                        <th rowspan="2">Designation</th>
                        <th rowspan="2">BS <br>Rate</th>
                        <th rowspan="2">Closing Balance<br>(B/F)</th>
                        <th colspan="3">Iqama Renewal Details</th>
                        <th colspan="3">Advance Details</th>
                        <th rowspan="2">Outstanding Balance</th>
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
                        @php
                            // Clean the value to ensure it is a valid number for comparison
                            $adv_bal = (float)str_replace(',', '', $row->advance_balance);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $row->employee_id }}</td>
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
                            <td class="text-right {{ $adv_bal < 0 ? 'bg-danger' : '' }}">
                                {{ number_format($row->advance_balance, 2) }}
                            </td>
                            <td class="text-right" style="font-weight: bold;">{{ number_format($row->final_balance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="sig-line"></div>
                           <p><b>{{ $login_name }}</b><br>Prepared By</p>
                        </td>
                        <td>
                            <div class="sig-line"></div>
                            <b> <br> Verified By</b>
                        </td>
                        <td>
                            <div class="sig-line"></div>
                            <b> <br> Authorized By</b>
                        </td>
                    </tr>
                </table>
            </div>
        </main>

        {{-- <div class="disclaimer">
            This is an official document of {{ $company->comp_name_en }}. Generated electronically on {{ $current_datetime }}.
            Any unauthorized alteration of this summary is strictly prohibited.
        </div> --}}
    </div>

</body>
</html>
