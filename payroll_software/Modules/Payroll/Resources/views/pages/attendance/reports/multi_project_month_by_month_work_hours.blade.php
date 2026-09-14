<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Working Hours</title>
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
        <div class="footer-date"> {{ date("Y/m/d") }}</div>
    </div>


    <!-- Table 1: Project-wise Salary & Workers Summary -->
    <div class="table-container">
        <div class="table-title">Project-wise Multiple Month Work Hours Statement</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">SN</th>
                    <th width="20%">Project Name</th>
                    @foreach ($months as $index => $m )
                    <th> {{ date("M", mktime(0, 0, 0, $m, 10));  }}-{{ substr($years[$index],2,2) }} </th>

                    @endforeach
                    <th>Total Hours</th>
                    <th width="21%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_hours_in_mounth = 0;
                    $total_hours_in_project = 0;
                    $monthly_hours_array = array_fill(0,count($months),0);
                    $ground_total_hours =0 ;

                @endphp
                @foreach($final_list as $sr)
                @php
                     // $total_hours_in_mounth += $sr->basic_hours + $sr->over_time;
                       $total_hours_in_project = 0;
                @endphp
                <tr >
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $sr->proj_name  }}</td>

                    @foreach ($sr->records as $index => $arecord )
                    @php
                          $total_hours_in_project += $arecord->basic_hours + $arecord->over_time;
                          $monthly_hours_array[$index] += $arecord->basic_hours + $arecord->over_time;
                          $ground_total_hours += $arecord->basic_hours + $arecord->over_time;
                    @endphp
                        <td> {{ $arecord->basic_hours+$arecord->over_time == 0 ? '-': $arecord->basic_hours+$arecord->over_time }}  </td>
                    @endforeach
                    <td>{{ $total_hours_in_project == 0 ? '-': $total_hours_in_project }}</td>
                    <td></td>

                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right"> Total</td>
                    @foreach ($monthly_hours_array as $index => $m )
                    <td>{{ $m }}</td>
                    @endforeach
                    <td>{{ $ground_total_hours }}</td>
                    <td></td>
                </tr>

            </tbody>
        </table>
    </div>



</body>
</html>

