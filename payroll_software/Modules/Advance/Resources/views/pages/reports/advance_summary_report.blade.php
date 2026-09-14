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
                   <img src="{{ asset($company->com_logo) }}"  alt="Logo not found" width="210px" height="80px">
                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">
                        {{ $company->comp_name_en }}
                     </div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address }}</p>
                </td>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Printed: {{ $current_datetime }}</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- Table -->
    <main>
        <div style="text-align: center; font-weight: bold; font-size: 15px; margin-bottom: 2px;">
            Employee Advance Summary of {{$projectName}}
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th rowspan="2" >S.N</th>
                    <th  rowspan="2" >ID</th>
                    <th  rowspan="2" >Employee</th>
                    <th  rowspan="2" >Iqama</th>
                    <th  rowspan="2" >Type</th>
                    <th  rowspan="2">Deduction <br> Iqama <br>Other </th>
                    <th colspan="4" style="text-align: center">Iqama</th>
                    <th colspan="3"  style="text-align: center">Other</th>
                    <th rowspan="2" >Outstanding</th>

                </tr>
                <tr>
                    <th>Expense</th>
                    <th>Cash Received</th>
                    <th>Deduction</th>
                    <th>Balance</th>
                    <th>Expense</th>
                    <th>Deduction</th>
                    <th>Balance</th>
                 </tr>
            </thead>
            <tbody>


                @php
                    $this_month_total_iqama_deduction = 0;
                    $this_month_total_other_deduction = 0;
                @endphp
                @foreach ($records as $row)
                @php
                     $this_month_total_iqama_deduction += $row->this_month_iqama_deduction;
                    $this_month_total_other_deduction += $row->this_month_other_deduction;
                @endphp
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $row->employee_id }}</td>
                        <td>{{ $row->employee_name }}</td>
                        <td>{{ $row->akama_no }}</td>
                        <td>{{ $row->hourly_employee == 1 ? "Hourly": "Basic" }}</td>
                        <td style="text-align: center">{{ $row->this_month_iqama_deduction }}/ {{ $row->this_month_other_deduction }}  </td>
                        <td class="text-right">{{ number_format($row->iqama_renewal, 2) }}</td>
                        <td class="text-right">{{ number_format($row->cach_received, 2) }}</td>
                        <td class="text-right">{{ number_format($row->iqama_deduction, 2) }}</td>
                        <td class="text-right">{{ number_format($row->iqama_renewal - ($row->iqama_deduction + $row->cach_received ), 2) }}</td>
                        <td class="text-right">{{ number_format($row->other_advance, 2) }}</td>
                        <td class="text-right">{{ number_format($row->other_advace_deduction, 2) }}</td>
                        <td class="text-right">{{ number_format($row->other_advace_deduction - $row->other_advance, 2) }}</td>
                        <td class="text-right">{{ number_format( $row->iqama_renewal + $row->other_advance - ($row->iqama_deduction + $row->cach_received + $row->other_advace_deduction ), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"> Total</td>
                        <td style="text-align: center">{{ $this_month_total_iqama_deduction }} <br> {{ $this_month_total_other_deduction }}  </td>
                        <td class="text-right" colspan="8"> </td>

                    </tr>
            </tfoot>
        </table>
    </main>
</body>
</html>
