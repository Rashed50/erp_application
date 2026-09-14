<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro.Emp. Work Summary
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
                max-width: 95%;
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
            width: 90%;
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
            background-color: #20d8e6;

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
        .td__friday {
            background-color: #AED6F1;
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
            padding: 0px;
            /* width:30px; */
        }
        .td__emplyoee_name {
            font-size: 12px;
            color: blue;
            text-align: left;
            padding-left: 3px;
            /* width:130px; */

        }

        .td__iqama {
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
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

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
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
        <h4 style="text-align: center; padding:10px;">Overtime  Working Sheet for the Date of {{ $working_date }} </h4>
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Emp. ID</th>
                        <th>Name of Employee</th>
                        <th>Iqama No</th>
                        <th>Designation </th>
                        <th>Working Project</th>
                        <th>IN </th>
                        <th>OUT</th>
                        <th>Basic Hours</th>
                        <th>O.T</th>
                        <th>Total Hours</th>
                        <th rowspan="{{ count($records) }}">Purpose Of Work</th>

                    </tr>
                </thead>
                <tbody>

                    @php

                    $gross_ot = 0;
                    $grand_hours = 0;
                    @endphp

                    @foreach($records as $emp)
                        @php
                            $grand_hours +=  $emp->daily_work_hours;
                            $gross_ot +=  $emp->over_time;
                        @endphp


                    <tr style="border-bottom:0;">

                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__employee_id"> {{$emp->employee_id}}</td>
                        <td class="td__emplyoee_name"> {{ $emp->employee_name }} </td>
                        <td class="td__iqama">{{$emp->akama_no}}</td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->catg_name,20) }} </td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->proj_name,20) }} </td>

                        <td  class="td__total">{{$emp->emp_io_entry_time}}</td>
                        <td  class="td__total">{{$emp->emp_io_out_time}}</td>
                        <td  class="td__total">{{$emp->daily_work_hours}}</td>
                        <td  class="td__total">{{$emp->over_time}}</td>
                        <td  class="td__total">{{ $emp->daily_work_hours + $emp->over_time }}</td>
                        {{-- <td class="td__emp_trade"> </td> --}}

                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8"> Total</td>
                        <td>{{$grand_hours  }}</td>
                        <td>{{ $gross_ot }}</td>
                        <td>{{ $grand_hours + $gross_ot }}</td>

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
