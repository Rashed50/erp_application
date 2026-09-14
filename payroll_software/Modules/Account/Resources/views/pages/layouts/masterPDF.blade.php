<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title', 'Payroll Report')</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15px 20px 30px 60px;
            @top-center { content: element(header); }
            @bottom-center { content: element(footer); }
        }

        body {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            position: relative;
        }

        /* Watermark - Every Page */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            /* transform: translate(-50%, -50%); */
            font-size: 80px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }

        /* Common header */
        .header {
            position: running(header);
            width: 100%;
            padding: 4px 0;
            border-bottom: 1.5px solid #6A7282;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .logo-cell {
            width: 130px;
            vertical-align: top;
        }

        .report-title {
            text-align: center;
            vertical-align: bottom; 
        }

        .report-title h1 {
            margin: 0;
            padding: 0;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .info-cell {
            width: 200px;
            text-align: right;
            vertical-align: top; 
        }

        .company-logo {
            width: 130px;
            height: auto;
            max-height: 60px;
            object-fit: contain;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
            margin-top: 0;
        }

        .arabic-text {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
        }

        .subtitle {
            font-size: 10px;
            color: #6e6a6a;
            margin-top: 0;
        }

        /* Common footer */
        .footer {
            position: running(footer);
            width: 100%;
            text-align: center;
            padding: 4px 0;
            border-top: 1px solid #6A7282;
            font-size: 9pt;
            color: #6A7282;
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            background: white;
        }

        .footer-date {
            position: absolute;
            right: 0;
            top: 8px;
        }

        /* Utility classes */
        .text-left   { text-align: left; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 3px 3px;
            text-align: center;
            border: 1px solid #000;
            font-size: 9px;
        }

        td {
            padding: 3px 4px;
            border: 1px solid #000;
            text-align: center;
            font-size: 9px;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .table-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            background-color: #d9d9d9;
            color: #000;
            padding: 2px;
            margin-bottom: 0;
            border: 1px solid #000;
            border-bottom: none;
        }

        .table-container {
            margin-bottom: 20px;
            page-break-inside: auto; /* Changed from avoid to auto */
        }

        /* Page break solutions */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid; /* Prevent sections from breaking */
        }

        .supplier-info-box {
            page-break-inside: avoid; /* Prevent supplier info from breaking */
            page-break-after: avoid;
        }

        /* Table row page break handling */
        tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Ensure table header repeats on each page */
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        /* Prevent page break inside important elements */
        .no-break {
            page-break-inside: avoid;
        }

        /* Custom yieldable sections styling placeholders */
        @yield('pdf-styles')
    </style>
</head>
<body>
    <!-- Watermark - Every Page -->
    <div class="watermark">
        ASLOOB BEDAA CO.
        <!-- <img width="400px" height="230px" src="{{ public_path('contents/admin/assets/images/logo_new.png') }}" alt="Company Logo"> -->
    </div>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
                         class="company-logo"
                         alt="Company Logo">
                </td>
                <td class="report-title" style="">
                    <h1>@yield('report_title', 'Report Title')</h1> 
                    <div class="subtitle">Printed at: @yield('print', Carbon\Carbon::now()->format('d M Y h:i A'))</div>   
                </td>
                <td class="info-cell">
                    <div class="company-name">@yield('company_name', 'ASLOOB BEDAA CO.')</div>
                    <div class="subtitle">@yield('company_address', 'Riyadh, Kingdom of Saudi Arabia')</div>
                    <!-- <div class="subtitle">@yield('company_email', 'info@asloobb.com')</div> -->
                    <!-- <div class="subtitle">@yield('company_phone', '+966506685890')</div> -->
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        System Generated Statement Authorized by Accounts Department
        <div class="footer-date">
            {{ $current_datetime ?? now()->format('d M, Y') }}
        </div>
    </div>

    <!-- Report Body -->
    <main>
        @yield('content')
    </main>
</body>
</html>