<!-- masterPDF.blade.php (Reusable Master for Reports) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title', 'Report')</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
            @top-center { content: element(header); }
            @bottom-center { content: element(footer); }
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
        }

        /* Header */
        .header {
            position: running(header);
            width: 100%;
            padding-bottom: 5mm;
            border-bottom: 1px solid #000;
            margin-bottom: 10mm;
        }

        .logo {
            width: 150px;
            height: auto;
            float: right;
        }

        .report-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin-top: 5mm;
            color: #003366;
        }

        .header-info {
            float: right;
            text-align: right;
            font-size: 9pt;
        }

        /* Footer */
        .footer {
            position: running(footer);
            width: 100%;
            text-align: center;
            padding-top: 3mm;
            border-top: 1px solid #6A7282;
            font-size: 8pt;
            color: #6A7282;
        }

        .footer-date {
            position: absolute;
            right: 0;
            top: 3mm;
        }

        /* Utility */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 3mm 4mm;
            border: 1px solid #000;
            text-align: left;
            font-size: 9pt;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .no-border {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 3mm;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .signature {
            margin-top: 10mm;
            width: 100%;
        }

        .signature td {
            border-top: 1px solid #000;
            padding-top: 2mm;
            text-align: center;
        }

        .disclaimer {
            font-size: 8pt;
            margin-top: 10mm;
            text-align: justify;
            color: #333;
        }

        /* Custom styles yield */
        @yield('pdf-styles')
    </style>
</head>
<body>
    <div class="watermark">ASLOOB BEDAA CO.</div>

    <div class="header">
        <img src="{{ public_path('contents/admin/assets/images/logo_new_2.png') }}" class="logo" alt="Logo">
        <div class="report-title">@yield('report-title')</div>
        <div class="header-info">
            @yield('header-info')
        </div>
    </div>

    <div class="footer">
        System Generated Statement Authorized by Accounts Department
        <div class="footer-date">{{ $current_datetime ?? now()->format('d M, Y') }}</div>
    </div>

    <main>
        @yield('content')
    </main>
</body>
</html>