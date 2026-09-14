<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $working_date }} Attendance</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15px 20px 30px 40px;

            /* Header */
            @top-center {
                content: element(header);
            }

            /* Footer */
            @bottom-center {
                content: element(footer);
            }

              .print__button {
                  visibility: hidden;
               }

               table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }


        }

        /* Header Styles */
        .header {
            position: running(header);
            width: 100%;
            text-align: center;
            padding: 2px 0;
            border-bottom: 1.5px solid #D1C097;
            margin-bottom: 15px;
        }


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
         border-top: 1px solid #D1C097;
         margin: 0;
         padding: 0;
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
           font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #222;
        }

        .arabic-text {
            font-family: 'amiri', DejaVu Sans, sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            text-align: right;
        }


        .subtitle {
            font-size: 12px;
            color: #666;
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
            bottom: 1px;
            left: 0;
            right: 0;
            background: white;
        }

        .footer-date {
            position: absolute;
            right: 0;
            top: 3px;
        }

        body {
              font-family: 'Time New Roman', Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

         .summary-container {
            margin-top:5px;
            width: 100%;
            page-break-inside: avoid; /* Ensures signatures don't split across pages */
        }

        .summary-table {
            width: 100%;
            /* border-collapse: collapse; */
            border: none !important;
        }


         /* Signature Section Styles */
        .signature-container {
            margin-top: 50px;
            width: 100%;
            page-break-inside: avoid; /* Ensures signatures don't split across pages */
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
        }

        .signature-table td {
            width: 33.33%;
            border: none !important;
            text-align: center;
            vertical-align: top;
            padding-top: 40px; /* Space for the actual signature */
        }

        .sig-line {
            border-top: 1px solid #000 !important;
            width: 50%;
            margin: 0 auto;
            padding-top: 5px;
        }

        .sig-title {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .sig-subtitle {
            font-size: 11px;
            color: #444;
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
            /* page-break-inside: avoid; */
            /* padding-right: 20px;
            padding-left: 20px; */

        }

         .table-container,     table { page-break-after:auto }
           , tr    { page-break-inside:avoid; page-break-after:auto }
           , td    { page-break-inside:avoid; page-break-after:auto }
           , thead { display:table-header-group }
           , tfoot { display:table-footer-group }

        .table-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
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


       .td__present {
            text-align: center;
            padding-right: 3px;
            font-weight: bold;
            background: #c5efd0;
            color: black;
            font-size: 14px;

        }
        .td__absent {
            text-align: center;
            padding-right: 3px;
            font-weight: bold;
            background: #ffc8ce;
            color: black;
            font-size: 14px;
        }

    </style>
</head>
<body>

      <!-- Watermark -->
    <div class="watermark">ASLOOB BEDAA CO.</div>
    <!-- Header -->
    <div class="header">

         <table class="header-table">

            <tr>
                <td style="width: 40%; align: left;">
                     {{-- <img src="{{ public_path('contents/admin/assets/images/abcc_logo_report.png') }}"
                        style="width: 130px; height: auto;"> --}}

                    <button type="" onclick="window.print()" id="print__button" class="print__button">Print</button>

                </td>
                <td style="width: 60%; text-align: right; padding-right: 30px; ">
                    <!-- <div class="company-name">ASLOOB BEDAA CO.</div>-->
                    <!--<div class="subtitle">{{ $company->comp_address }}</div>-->
                    <img src="{{ asset($company->com_logo) }}"  alt="" width="230px" height="70px">
                </td>
            </tr>
         </table>

        {{-- <hr class="top-hr"> --}}
    </div>

    <!-- Footer -->
    {{-- <div class="footer">
        System Generated Attendance Sheet Verified By Project Management Team
        <div class="footer-date">{{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
    </div> --}}


    {{-- <p style=" font-size:14px;padding-left:10px;">
        <b>Project Name:</b> {{ $project_name == null ? '': $project_name }} <br>
        <b>Working Shift:</b> {{ $working_shift }} <br>
        <b>Attendance Date:</b> {{ $working_date }}
    </p> --}}



     <div style="summary-container">

        <table class="summary-table">
            <tr>
                <td style=" font-weight: bold; padding-left:30px; text-align:left; font-size: 12px;text-transform: uppercase; border: none !important;">
                         Project Name: {{ $project_name == null ? '': $project_name }}
                        <br>
                        Attendance Date: {{ $working_date }} <br>
                        Working Shift: {{ $working_shift }}

                </td>

                <td style=" font-weight: bold; padding-left:10px;  text-align:left;font-size: 12px;text-transform: uppercase; border: none !important;">
                         Basic Employees : {{$total_basic_emps  }}
                        <br>
                        Hourly Employees : {{ $total_hourly_emps }}
                 </td>
            </tr>
        </table>
    </div>
    <!-- Table 1: Workers Summary -->
    <div style="width: 100%; height:auto; margin: 0 auto; padding:20px; display: flex; flex-direction: row; gap: 32px;">
        <table style="width:45%; height:auto;   font-size:14px;font-weight:bold;">
            <tbody>
                    <tr>
                        <td colspan="2" style=" text-align: center;background:#182F49;color:white;padding-top:5px;padding-bottom:5px;font-size: 14px;">Attendance Summary</td>
                    </tr>


                    <tr style="background-color: lightgray">
                        <td class="text-left"> Total Workforce</td>
                        <td  class="text-center"> {{ count($employees) }}</td>
                    </tr>
                    <tr style="background-color: #c5efd0;">
                        <td class="text-left"> Total Present</td>
                        <td  class="text-center"> {{ $total_present - $total_sick_leave  }}</td>
                    </tr>
                    <tr style="background-color: #ffc8ce">
                        <td class="text-left"> Total Absent</td>
                        <td class="text-center">  {{ count($employees) - $total_present }} </td>
                    </tr>
                     <tr>
                        <td class="text-left" style="color: black;">Leave Taken</td>
                        <td  class="text-center" style="color: black;"> {{ $total_sick_leave  }}</td>
                        <!--== 0 ? '-' : $total_sick_leave-->
                    </tr>

            </tbody>
        </table>

        <table style="width:45%; height:auto;   font-size:14px;font-weight:bold;">
            <tbody>
                    <tr>
                        <td colspan="2" style="  text-align: center;background:#182F49;color:white;padding-top:5px;padding-bottom:5px;font-size: 14px;">Sponsor Summary</td>
                    </tr>
                   @php
                        $total_others_emp = 0;
                    @endphp

                    @foreach ($subcon_emp_summary as $ses )
                    @php
                        $total_others_emp += $ses->total_present_employee ;
                    @endphp
                    @endforeach

                     <tr>
                        <td class="text-left"> ASLOOB</td>
                        <td class="text-center">  {{ $total_present - $total_others_emp }} </td>
                    </tr>
                    @foreach ($subcon_emp_summary as $ses )
                     <tr>
                        <td class="text-left"> {{ $ses->spons_name }}</td>
                        <td class="text-center"> {{ $ses->total_present_employee }} </td>
                    </tr>
                    @endforeach

            </tbody>
        </table>
    </div>

    <!-- Table 2: Date-wise Payment Summary -->
    <div class="table-container">
        <div class="table-title"  style="background: #182F49;color:white; text-align:center; font-size:14px;"> Daily Attendance Sheet as on {{ $working_date }}</div>
        <table>
            <thead>
                 <tr style="background: #D1C097">
                    <th>S.N</th>
                    <th>Emp. ID </th>
                    <th>Employee Name</th>
                    <th>Iqama No</th>
                    <th>Designation</th>
                    <th>Sponsor</th>
                    <th>Salary Type</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
            </thead>
            <tbody>

                        @php
                            $total_present = 0;
                            $total_absent = 0;
                            $total_sick_leave = 0;
                        @endphp

                        @foreach($employees as $ar)
                            @php
                            $total_present += $ar->is_present ==  true ? 1:0;
                            @endphp
                        <tr>
                            <td class="td__sn"> {{$loop->iteration}}</td>
                            <td class="td__employee_id">{{$ar->employee_id }}</td>
                            <td class="text-left">{{$ar->employee_name }}</td>
                            <td class="td__iqama">{{$ar->akama_no}}</td>
                            <td class="td__sn">{{$ar->catg_name }}</td>
                            <td class="td__sn">{{$ar->spons_name }}</td>
                            <td class="td__sn">{{$ar->hourly_employee == null ? 'Basic':'Hourly' }}</td>
                             @if($ar->is_present)
                                 <td class="td__present">P{{ $ar->attend_record->attendance_status == 'AW' ? '':'-'.$ar->attend_record->attendance_status}}</td>
                                <td class="td__sn"></td>
                            @else

                            <td class="td__sn"></td>
                            <td class="td__absent">A</td>
                            @endif

                        </tr>
                        @endforeach

            </tbody>
        </table>
    </div>

     <!-- NEW: Signature Section -->
    <div class="signature-container">
        <br><br>
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-title">Prepared By </div>
                    <div class="sig-subtitle">Site Admin / Timekeeper</div>
                </td>
                <td>

                </td>
                <td>
                    <div class="sig-line"></div>
                    <div class="sig-title">Approved By</div>
                    <div class="sig-subtitle">Project Engineer / Manager</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

