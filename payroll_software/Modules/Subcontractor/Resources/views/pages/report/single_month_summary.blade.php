<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Subcont. Statement</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15px 20px 30px 60px;

            /* Header */
            @top-center {
                content: element(header);
            }

            /* Footer */
            @bottom-center {
                content: element(footer);
            }
        }

        /* Header Styles */
        /* .header {
            position: running(header);
            width: 100%;
            text-align: center;
            padding: 8px 0;
            border-bottom: 1.5px solid #6A7282;
            margin-bottom: 15px;
        } */


            .watermark {
         position: fixed;
         top: 40%;
         left: 15%;
         opacity: 0.05;
         font-size: 100px;
         transform: rotate(-30deg);
         z-index: -1;
      }

          .top-hr {
         border: 0;
         border-top: 3px solid lightblue;
         margin: 0;
         margin-bottom: 5px;
      }

          header {
            position: running(header);
            top: -50px; /* Adjust as needed */
            left: 0;
            right: 0;
            height: 50px;
            /* text-align: center; */
            /* line-height: 35px; */
            font-size: 14px;
        }

        .header-table {
         width: 100%;
         margin-bottom: 12px;
         border-collapse: collapse;
         margin: 0;
         border: none;

      }


      .header-table td {
         vertical-align: middle;
             border: none;
      }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .arabic-text {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
        }

        .report-title {
            font-size: 12px;
            /* font-weight: bold; */
            color: #000;
        }

        /* Footer Styles */
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

        body {
            font-family: 'amiri', DejaVu Sans, sans-serif;;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        /* Subcontractor Info */
        .subcontractor-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 8px;
            /* background-color: #f5f5f5; */
            border: 1px solid #ddd;
        }

        .info-left {
            font-weight: bold;
        }

        .info-right {
            font-weight: bold;
        }

        /* Table Styles */
        .table-container {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .table-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            background-color: #d9d9d9;
            color: #000;
            padding: 6px;
            margin-bottom: 0;
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000;
            font-size: 12px;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #000;
            text-align: center;
            font-size: 12px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: right;
        }

        .closing-balance-container {
            display: table;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .left-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .right-column {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            padding-left: 10px;
        }

        .amount-words-section {
            padding: 15px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .amount-label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .amount-line {
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
            margin-top: auto;
            font-style: italic;
            min-height: 20px;
               font-weight: bold;
        }

        .authorization {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
        }

        /* Ensure no page break in this section */
        .closing-balance-container {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

      <!-- Watermark -->
    <div class="watermark">ASLOOB BEDAA CO.</div>
    <!-- Header -->
    <div class="header">
        {{-- <div class="company-name">
            ASLOOB BEDAA CONTRACTING CO.

        </div>

        <div class="report-title">Riyadh, Kingdom of Saudi Arabia</div>
        <div class="report-title">SUBCONTRACTOR PAYMENT STATEMENT – {{ $report_month }}</div> --}}
         <table class="header-table">

            <tr>
                <td style="width: 40%; align: left;">
                    {{-- <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
                    style="width: 130px; height: auto;"> --}}
                    {{-- <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px"> --}}
                </td>
                <td style="width: 60%; text-align: right;">
                     {{-- <div class="company-name">ASLOOB BEDAA CONTRACTING CO.</div>
                    <div class="subtitle">{{ $company->comp_address }}</div> --}}
                    {{-- <div class="subtitle">+966 54 765 7236, {{ $company->comp_phone1 }} </div>
                    <div class="subtitle">{{ $company->comp_email1 }} </div> --}}
                     <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
                </td>
            </tr>
                    {{-- <tr><td colspan="2" style="text-align: center"><h4>BILL INVOICE</h4></td></tr> --}}
        </table>

        <hr class="top-hr">
    </div>

    <!-- Footer -->
    <div class="footer">
        System Generated Statement Authorized by Accounts Department
        <div class="footer-date">{{ $current_datetime }}</div>
    </div>

    <!-- Subcontractor Information -->
    <div class="subcontractor-info">
        <div class="info-left"><b>Pay To:</b>
            <br> {{ $subcontractor_info->subcon_name }}
            <br> Iqama: {{ $subcontractor_info->iqama_no }}
            <br>Mobile: {{ $subcontractor_info->mobile_no }}
            <br>
            {{-- {{ $subcontractor_info->subcon_email }} --}}
        </div>
        <div class="info-right">Statement of {{ date("F", mktime(0, 0, 0, $month, 10)) }}, {{ $year }}</div>
    </div>

    <!-- Table 1: Project-wise Salary & Workers Summary -->
    <div class="table-container">
        <div class="table-title">Project-wise Salary & Workers Summary</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">SN</th>
                    <th width="35%">Project Name</th>
                    <th width="12%">Total Employees</th>
                    <th width="12%">Total Hours</th>
                    <th width="15%">Total Salary</th>
                    {{-- <th width="15%">Cumulative Total</th> --}}
                    <th width="21%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_service_emps_in_mounth = 0;
                    $total_service_hours_in_mounth = 0;
                    $total_service_amount_in_mounth = 0;


                @endphp
                @foreach($subcontractor_info->service_records as $sr)
                @php
                     $total_service_emps_in_mounth += $sr->total_emp;
                     $total_service_hours_in_mounth += $sr->total_hours;
                     $total_service_amount_in_mounth += $sr->total_salary;

                @endphp
                <tr >
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $sr->proj_name  }}</td>
                    <td>{{ $sr->total_emp  }}</td>
                    <td>{{ $sr->total_hours }}</td>
                    <td class="text-right">{{ $sr->total_salary }}</td>
                    {{-- <td class="text-right">{{ $project['cumulative_total'] }}</td> --}}
                    <td>{{ $sr->remarks }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2"  class="text-right"> Total </td>
                    <td>{{ $total_service_emps_in_mounth }}</td>
                    <td>{{ $total_service_hours_in_mounth }}</td>
                    <td class="text-right">{{ $total_service_amount_in_mounth }}</td>
                    <td> </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Table 2: Date-wise Payment Summary -->
    <div class="table-container">
        <div class="table-title">Date-wise Payment Summary</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">SN</th>
                    <th width="25%">Paid Date</th>
                    <th width="15%">For Month</th>
                    {{-- <th width="15%">Subcontractor Name</th> --}}
                    <th width="10%">Method</th>
                    <th width="12%">Paid By</th>
                    <th width="15%">Amount</th>
                    <th width="18%">Remaining Balance</th>
                    <th width="15%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_paid_amount = 0;
                @endphp
                @foreach($subcontractor_info->payment_records as $pr)
                @php
                       $total_paid_amount += $pr->grand_total;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pr->payment_date  }}</td>
                    <td>{{ date("F", mktime(0, 0, 0, $pr->month, 10)) }}, {{ $pr->year }}</td>
                    {{-- <td class="text-left">{{ $pr->subcon_name  }}</td> --}}
                    <td>{{ $pr->payment_method  == 1? 'Cash Paid':'Bank Paid' }}</td>
                    <td>{{ $pr->name  }}</td>
                    <td class="text-right">{{ $pr->grand_total  }}</td>
                    <td class="text-right">{{ $total_service_amount_in_mounth - $total_paid_amount  }}</td>
                    <td>{{ $pr->remarks }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right"> Total </td>
                    <td class="text-right">{{  $total_paid_amount  }}</td>
                    <td></td>
                    <td></td>

                </tr>
            </tbody>
        </table>
    </div>



    <!-- Closing Balance and Amount in Words Section -->
    {{-- <div class="closing-balance-container">

        <div class="left-column">
            <div class="table-title">Closing Balance Summary</div>
            <table>
                <thead>
                    <tr>
                        <th width="70%">Description</th>
                        <th width="30%">Amount (SAR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="total-row">
                        <td class="text-left">Total Salary</td>
                        <td class="text-right">{{ $total_service_amount_in_mounth }}</td>
                    </tr>
                     <tr class="total-row">
                        <td class="text-left">Total Paid</td>
                        <td class="text-right">{{ $total_paid_amount }}</td>
                    </tr>
                     <tr class="total-row">
                        <td class="text-left">Remaining Balance</td>
                        <td class="text-right">{{ $total_service_amount_in_mounth-$total_paid_amount }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-left">Previous Closing Balance</td>
                        <td class="text-right">{{ $subcontractor_info->upto_last_month_balance }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-left">Net Payable Amount</td>
                        <td class="text-right">{{ $subcontractor_info->upto_last_month_balance + $total_service_amount_in_mounth - $total_paid_amount }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="right-column">
            <div class="amount-words-section">
                <div class="amount-label">Amount in Words:</div>
                <div class="authorization"> </div>
            </div>
        </div>
    </div> --}}

</body>
</html>

