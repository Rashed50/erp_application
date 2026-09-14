<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Work Update History</title>
    <style>
        /* 1. GLOBAL FONT SETTING */
        * {
            /* This ensures every single element uses the same font */
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

        /* A4 Wrapper */
        .page-container {
            background-color: white;
            width: 297mm;      /* Swapped: was 210mm for landscape view */
            min-height: 210mm; /* Swapped: was 297mm */
            padding-left: 12mm;       /* Internal page margins */
            padding-right: 7mm;
            padding-top: 10mm;
            padding-bottom: 7mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            box-sizing: border-box;
            position: relative;
        }

        @media print {
            body { background-color: white; padding: 0; display: block; }
            @page { size: A4 landscape; margin: 5; }
            .page-container { width: 100%; box-shadow: none; margin: 0; padding: 15mm; }
            .no-print { display: none !important; }

            /* Force background colors for printers */
            #employeeinfo th {
                background-color: #FFD700 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-success { background-color: #d4edda !important; -webkit-print-color-adjust: exact; }
            .bg-danger { background-color: #f8d7da !important; -webkit-print-color-adjust: exact; }
        }

        /* 2. TABLE & CONTENT STYLES */
        header {
            width: 100%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        #employeeinfo {
            width: 100%;
            border-collapse: collapse;
        }

        #employeeinfo th, #employeeinfo td {
            border: 0.5px solid lightgrey; /* Slightly darker borders for clarity */
            padding: 8px;
            font-size: 12px; /* Consistent size for table data */
        }

        #employeeinfo th {
            background-color: #FFD700;
            font-weight: bold;
            text-align: center;
        }

        .td__center { text-align: center; }
        .td__name { text-align: left; }

        /* Status Colors */
        .bg-success { background-color: #d4edda; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }

        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .print__button {
            background: #000;
            color: #fff;
            border: none;
            padding: 8px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="page-container">
        <header>
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 25%; text-align: left; border: none;">
                        <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}" alt="Logo" style="height: 60px;">
                    </td>
                    <td style="width: 50%; text-align: center; border: none;">
                        <div style="font-size: 18px; font-weight: bold;">{{ $company->comp_name_en }}</div>
                        <div style="font-size: 10px;">{{ $company->comp_address }}</div>
                    </td>
                    <td style="width: 25%; text-align: right; border: none; font-size: 10px;">
                        Date: {{ date('Y-m-d') }} <br><br>
                        <button onclick="window.print()" class="print__button no-print">PRINT</button>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <h3 style="text-align:center; font-weight: bold;">Employee Workinf Record Update History</h3>

            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Emp. ID</th>
                        <th>Employee Name</th>
                        <th>Iqama</th>
                        <th>Trade</th>
                        <th>Sponsor</th>
                        <th>Working Project</th>
                        <th>Month <br>Year</th>
                        <th>W.Days <br>Hours</th>
                        <th>Operation</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grand_total_days = 0; $grand_total_hours = 0; @endphp
                    @foreach($records as $ar)
                        @php
                            $grand_total_days += $ar->total_day;
                            $grand_total_hours += $ar->total_hour;
                        @endphp

                        <tr>
                            <td class="td__center">{{ $loop->iteration }}</td>
                            <td>{{ $ar->employee_id }}</td>
                            <td class="td__name">{{ $ar->employee_name }}</td>
                            <td class="td__center">{{ $ar->akama_no }}</td>
                            <td class="td__center">{{ $ar->catg_name }}</td>
                            <td class="td__center">{{ $ar->spons_name }}</td>
                            <td class="td__center">{{ $ar->proj_name }}</td>
                            <td class="td__center">
                                @if($ar->month && $ar->year)
                                    {{ date("M", mktime(0, 0, 0, $ar->month, 10)) }}-{{ Str::substr($ar->year, -2) }}
                                @else N/A @endif
                            </td>
                            <td class="td__center">{{ $ar->total_day }} days  <br> {{ $ar->total_hour }} hrs</td>
                            <td class="td__center">{{ $ar->operation_type ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->updated_by_name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <table class="signature-table" style="border: none;">
                <tr style="border: none;">
                    <td style="width: 50%; text-align: left; border: none;">
                        <p style="margin-bottom: 40px;"><b>----------------------</b></p>
                        <p><b>{{ $login_name }}</b><br>Prepared By</p>
                    </td>
                    <td style="width: 50%; text-align: right; border: none;">
                        <p style="margin-bottom: 40px;"><b>-----------------------------</b></p>
                        <p><b>Authorized Signature</b></p>
                    </td>
                </tr>
            </table>
        </main>
    </div>

</body>
</html>
