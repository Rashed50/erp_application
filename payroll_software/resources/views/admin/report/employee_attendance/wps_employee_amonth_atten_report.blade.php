<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WPS Work Records
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
                max-width: 100%;
                margin: 0 auto 10px;

            }

            @page {
                size: A4 landscape;
                margin: 5mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: #AED6F1;
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
            width: 96%;
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
            border: 0.5px solid gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        /* table tr {} */

        table th {
            font-size: 12px;
            border: 0.5px solid gray;
            font-weight: bold;
            color: #000;
            padding: 3px;

            background:rgb(40, 120, 185)

        }

        table td {
            text-align: center;
            font-size: 12px;
            border: 0.5px solid gray;
            padding: 2px;
            margin: 0px;
            height:20px;

            /* Top,Right,Bottom,left */
        }

        .td__s_n {
            font-size: 12px;
            color: black;
            text-align: center;
            width:30px;
        }

        .td__employee_id {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: bold;
            padding: 2px;
            /* width:30px; */
        }
        .td__emplyoee_name {
            font-size: 12px;
            color: black;
            text-align: left;
            padding-left: 3px;
            /* width:130px; */

        }

        .td__iqama {
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding: 2px;
            /* width:50px; */
        }

        .td__emp_trade {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: center;

        }


        .td__total {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: 300;

        }
         .td__ground_total {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: bold;

        }
         .td__note {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: bold;
            width: 100px;

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
                <p>   </p>
             </div>
            <!-- title -->
            <div class="title__part">
                <h3>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h3>
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
        <h4 style="text-align: center; padding:10px;">WPS Employee Attendance Summary of  {{ $month_name }} {{ $year }} </h4>
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Iqama No</th>
                        <th>Designation </th>
                        <th>IBAN</th>
                        <th>Bank</th>
                        <th>Working Project</th>
                        <th>Total Hours</th>
                        <th>Basic Hours</th>
                        <th>Over <br>Time</th>
                        <th>Working <br> Days</th>
                        <th>Paid <br>Leave</th>
                          <th>Total Days</th>
                        <th>Absent</th>
                        <th>Note</th>
                        <th>Signature</th>

                    </tr>
                </thead>
                <tbody>

                    @php

                    $total_ot = 0;
                    $total_basic_hours = 0;
                    $total_sick_leave = 0;
                    @endphp

                    @foreach($final_records as $emp)
                    @php
                            $total_basic_hours +=  $emp->basic_hours;
                            $total_ot +=  $emp->over_time;
                            $total_sick_leave +=  $emp->sick_leave;

                        @endphp


                   
                    <tr style="border-bottom:0;">
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__employee_id"> {{$emp->employee_id}}</td>
                        <td class="td__emplyoee_name"> {{ $emp->acc_holder_name }} </td>
                        <td class="td__iqama">{{$emp->akama_no}}</td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->catg_name,20) }} </td>
                        <td class="td__iqama">{{$emp->acc_iban}}</td>
                        <td class="td__iqama">{{$emp->bn_name}}</td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->last_working_project,20) }} </td>
                        <td  class="td__total">{{ $emp->basic_hours + $emp->over_time }}</td>
                        <td  class="td__total">{{$emp->basic_hours}}</td>
                        <td  class="td__total">{{$emp->over_time}}</td>
                        <td  class="td__total">  {{ $emp->duty_status == 'Full' ? 'Full' : $emp->working_days.'' }} </td>
                        <td  class="td__total">{{ $emp->sick_leave == 0 ? '-': $emp->sick_leave }}</td>
                          <td  class="td__total">{{ $emp->present == 0 ? '-' : $emp->present.'' }}</td>
                        <td  class="td__total">{{ $emp->absent == 0 ? '-' : $emp->absent.'' }}</td>
                        <td class="td__note"> </td>
                        <td class="td__note"> </td>



                    </tr>
                    @endforeach

                     <tr>
                        <td colspan="8"> Total</td>
                        <td>{{ $total_basic_hours + $total_ot }}</td>
                        <td>{{ $total_basic_hours }}</td>
                        <td>{{ $total_ot }}</td>
                        <td></td>
                        <td>{{$total_sick_leave  }}</td>
                        <td></td>
                         <td></td>
                          <td></td>
                    </tr>

                </tbody>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br>
    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 60px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
              <p>   <b> {{$login_name}} </b> <br> Prepared By</p>
                <p><br>Checked By</p>
                <p><br>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>
