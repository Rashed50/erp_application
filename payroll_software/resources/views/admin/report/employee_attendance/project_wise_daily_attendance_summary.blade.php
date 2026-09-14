<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 90%;
                margin: 0 auto 10px;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }
        }


        .main__wrap {
            width: 90%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part h6 {
            color: rgb(38, 104, 8);
            text-align: center;
            font-size:18px;
        }

        .title__part{
            text-align: center;
            font-size:18px;
        }
        .title_left__part{
            text-align: left;
            font-size:11px;
        }
        .address {
            font-size:13px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
          border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 10px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        }
 
        .td__project_name {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        

        .td__amount {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-right:10px;
        }
        .td__total_amount {
            font-size:10px;
            color: black;
            font-weight: bold;
            text-align: center;
            padding-right:10px;
        }



    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong>  Employee Attendance Summary at {{$report_title[0]}}</strong></p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th > <span>S.N</span> </th> 
                  <th> <span>Project Name</span> </th>
                  <th> <span>Total Employees</span> </th>
                  <th> <span>Working Time</span> </th>
                  <th> <span>Today Present</span> </th>
                  <th> <span>Absent</span> </th>
                  <th> <span>Total Hours</span> </th>
                  <th> <span>Total OT </span> </th>
                  
                </tr>
                </thead>
                <tbody>
                    @php 
                        $grand_total_working_hours = 0;
                        $grand_total_over_time = 0;
                        $grand_total_active_emp = 0;
                        $grand_total_present_emp = 0;
                        $grand_total_absent_emp = 0;
                  
                    @endphp

                  @foreach ($attendance_summary_records as $attendence)
                    @php 
                        $grand_total_working_hours += $attendence->total_daily_work_hours  ;
                        $grand_total_over_time += $attendence->total_over_time;
                        $grand_total_active_emp += $attendence->total_active_emp;
                        $grand_total_present_emp += $attendence->total_present_emp;
                        $grand_total_absent_emp += ($attendence->total_active_emp - $attendence->total_present_emp);
                    @endphp
               
                <tr style="text-align: center;">
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__project_name"> {{ $attendence->project_name }} </td>
                  <td class="td__amount"> {{ $attendence->total_active_emp}}
                     
                </td>
                @if($attendence->emp_io_shift == 0)  
                    <td class="td__amount"> {{ 'Day Shift'  }} 
                    </td>
                @elseif($attendence->emp_io_shift == 1)  
                    <td class="td__amount"> {{ 'Night Shift'  }} 
                    </td>
                @else  
                    <td class="td__amount"> {{  'Day+Night'   }} 
                    </td>
                @endif
                 
                  <td class="td__amount"> {{ $attendence->total_present_emp }} </td>
                  <td class="td__amount"> {{ $attendence->total_absent >= 0 ? $attendence->total_absent : '-' }} </td>
                  <td class="td__amount"> {{ $attendence->total_daily_work_hours }} </td>
                  <td class="td__amount"> {{ $attendence->total_over_time }} </td>
                 </tr>
                @endforeach  
                <tr style="text-align: center;">
                    <td class="td__total_amount">   </td> 
                    <td class="td__total_amount">   </td>                   
                    <td class="td__total_amount"> {{ $grand_total_active_emp }} </td>
                    <td class="td__total_amount"> </td>
                    <td class="td__total_amount">{{  $grand_total_present_emp }}  </td>
                    <td class="td__total_amount">{{  $grand_total_absent_emp }}  </td>
                    <td class="td__total_amount">{{  $grand_total_working_hours }}  </td>
                    <td class="td__total_amount"> {{  $grand_total_over_time }}  </td>
                   </tr>
              </tbody>
            </table>
        </section>
        <!-- ---------- -->

    <section>
        <br><br>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br></p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>
