<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atten.  Records
    </title>
    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 99%;
                margin: 0 auto 10px;

            }

            @page {
                size: A4 landscape;
                margin: 10mm 0mm 5mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: red;
                -webkit-print-color-adjust: exact;
            }

            th.th_header, td.th_header {
                background-color: #B3E6FA;
                -webkit-print-color-adjust: exact;
            }




            /* th.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            } */

            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 95%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
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
            border: 1px solid #333;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        /* table tr {} */
        table th {
            font-size: 11px;
            border: 1px solid #333;
            font-weight: bold;
            color: #000;

        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px solid #333;
            padding: 0px;
            margin: 0px;
            height:20px;
            /* Top,Right,Bottom,left */
        }
        .td__friday {
            background-color: red;
        }

        .th_header {
            background-color: #B3E6FA;
            height: 30px;
            font-size: 12px;
            font-weight: bold;
        }

        .project_th_header {
            background-color: #B3E6FA;
            height: 15px;
            font-size: 10px;
            font-weight: bold;
            padding: 2px;
        }

        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;
            width:20px;
        }

        .td__month_year {
            font-size:11px;
            color: black;
            text-align: center;
            padding:0px;
        }

        .td__project {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-left: 2px;
            width:100px;
        }

        .td__day{
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding: 0px;
            margin:0px;
        }
        .td__absent{
            font-size: 11px;
            color: red;
            font-weight: bold;
            text-align: center;
            padding-right: 2px;
            margin:0px;

        }

        .td__total {
            font-size: 11px;
            color: black;
            text-align: right;
            font-weight: bold;
            padding-right: 2px;
        }

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
        }

        .em_td_header {
            background-color: #B3E6FA;
             font-size: 12px;
            font-weight: bold;
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
                <p> <strong class="td__red__color"></strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address> <br>
                <!-- <p>An Employee Last One Year Attendance Record Details.</p> -->
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

                <tr>
                    <td class="em_td_header">Employee ID</td>
                    <td>{{$emp->employee_id}}</td>
                    <td  class="em_td_header">Mobile Number</td>
                    <td>{{$emp->mobile_no}}</td>
                    <td class="em_td_header">Sponsor</td>
                    <td>{{$emp->spons_name}}</td>
                </tr>
                <tr>
                    <td class="em_td_header">Employee Name</td>
                    <td>{{$emp->employee_name}}</td>
                    <td class="em_td_header">Iqama Number</td>
                    <td>{{$emp->akama_no}}</td>
                    <td class="em_td_header">Job Status</td>
                    <td>{{$emp->title}}</td>
                </tr>
                <tr>
                    <td class="em_td_header">Camp Name</td>
                    <td>{{$emp->ofb_name}}</td>
                </tr>
            </table>

        </section>

        <section class="table__part">
            <table>
                 <caption style="padding-top: 5px;">An Employee Attendance Details</caption>
                <thead>
                    <tr>
                        <th class="th_header" >S.N</th>
                        <th class="th_header">Day</th>
                        <th class="th_header">Month,Year</th>
                        <th class="th_header">Inserted At </th>
                        <th class="th_header">Shift</th>
                        <th class="th_header">IN </th>
                        <th class="th_header">OUT </th>
                        <th class="th_header">Hours </th>
                        <th class="th_header">Over Time </th>
                        <th class="th_header">Total Hours </th>
                        <th class="th_header">Working Project </th>                        
                        <th class="th_header">Time Keeper </th>

                    </tr>
                </thead>
                <tbody>


                     @php
                        $total_ot = 0;
                        $basic_hours = 0;
                    @endphp

                    @foreach($attendance_records as $arecord)
                    @php
                        $total_ot += $arecord->over_time;
                        $basic_hours += $arecord->daily_work_hours;
                    @endphp

                    <tr style="border-bottom:0;">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $arecord->emp_io_date  }}</td>
                        <td>{{ date("F", mktime(0, 0, 0,  $arecord->emp_io_month, 10))  }},{{ $arecord->emp_io_year }}</td>
                        <td>{{ $arecord->emp_io_entry_date }}</td>
                        <td>{{ $arecord->emp_io_shift == 0 ? "Day":"Night"  }}</td>
                        <td>{{ $arecord->emp_io_entry_time }}</td>
                        <td>{{ $arecord->emp_io_out_time }}</td>
                        <td>{{ $arecord->daily_work_hours}}</td>
                        <td>{{ $arecord->over_time }}</td>
                        <td>{{ $arecord->daily_work_hours + $arecord->over_time  }}</td>
                        <td>{{ $arecord->proj_name }}</td>
                        <td>{{ $arecord->name}}</td>
                      </tr>
                @endforeach
                <tr>
                    <td colspan="7">Total</td>
                    <td>{{  $basic_hours }}</td>
                     <td>{{  $total_ot }}</td>
                      <td>{{   $basic_hours +  $total_ot }}</td>
                </tr>


                </tbody>
                
            </table>
        </section>
         
        <!--  Signature ---------- -->
        <br>
        <section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 60px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; text-align: center; ">
                    <p>  <b>  ------------------------ <br> {{$login_name}} </b> <br> Prepared By  </p>
                    <p>  <b>  ------------------------ <br>   </b> Accounts/HR  </p>
                    <p>  <b>  ------------------------ <br>   </b> Approved By  </p>
                </div>
            </div>
            <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p>
        </section>
    </div>
</body>

</html>
