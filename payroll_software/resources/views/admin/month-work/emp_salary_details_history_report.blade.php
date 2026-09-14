<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salary Details History Report</title>
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
            border: 0.5px solid lightgrey;
            padding: 8px;
            font-size: 11px;
        }

        #employeeinfo th {
            background-color: #FFD700;
            font-weight: bold;
            text-align: center;
        }

        .td__center { text-align: center; }
        .td__name { text-align: left; }

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
            <h3 style="text-align:center; font-weight: bold;">Employee Salary Details History Report</h3>

            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Emp. ID</th>
                        <th>Employee Name</th>
                        <th>Iqama</th>
                        <th>Trade</th>
                        <th>Sponsor</th>
                        <th>Month, Year</th>
                        <th>Basic Amount<br>Hours</th>
                        <th>Hourly Employee<br>Hourly Rate</th>
                        <th>Food Amount</th>
                        <th>Payment Method</th>
                        <th>Updated By Name</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_basic_amount = 0;
                        $total_basic_hours = 0;
                        $total_hourly_rate = 0;
                        $total_food_amount = 0;
                    @endphp
                    @foreach($records as $ar)
                        @php
                            $total_basic_amount += $ar->basic_amount;
                            $total_basic_hours += $ar->basic_hours;
                            $total_hourly_rate += $ar->hourly_rate;
                            $total_food_amount += $ar->food_allowance;
                        @endphp

                        <tr>
                            <td class="td__center">{{ $loop->iteration }}</td>
                            <td>{{ $ar->employee_id }}</td>
                            <td class="td__name">{{ $ar->employee_name }}</td>
                            <td class="td__center">{{ $ar->akama_no }}</td>
                            <td class="td__center">{{ $ar->catg_name }}</td>
                            <td class="td__center">{{ $ar->spons_name }}</td>
                            <td class="td__center">
                                @if($ar->salary_month && $ar->salary_year)
                                    {{ date("M", mktime(0, 0, 0, $ar->salary_month, 10)) }}-{{ Str::substr($ar->salary_year, -2) }}
                                @else N/A @endif
                            </td>
                            <td class="td__center">{{ number_format($ar->basic_amount, 2) }}<br>{{ $ar->basic_hours ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->hourly_employee == 1 ? 'Yes' : 'No' }}<br>{{ number_format($ar->hourly_rate, 2) }}</td>
                            <td class="td__center">{{ number_format($ar->food_allowance, 2) }}</td>
                            <td class="td__center">{{ ucfirst($ar->payment_method ?? 'N/A') }}</td>
                            <td class="td__center">{{ $ar->updated_by_name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #f9f9f9;">
                    <tr>
                        <td colspan="7" style="font-weight:bold; text-align:right;">TOTAL:</td>
                        <td class="td__center" style="font-weight:bold;">{{ number_format($total_basic_amount, 2) }}<br>{{ $total_basic_hours }}</td>
                        <td class="td__center" style="font-weight:bold;">{{ number_format($total_hourly_rate, 2) }}</td>
                        <td class="td__center" style="font-weight:bold;">{{ number_format($total_food_amount, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
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
