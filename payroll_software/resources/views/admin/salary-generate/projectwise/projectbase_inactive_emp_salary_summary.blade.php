<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Summary Report
    </title>



    <!-- style -->
    <style>

 .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        body {
            overflow: hidden;
        }


        /* Preloader */

        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #fff;
            /* change if the mask should have another color then white */
            z-index: 99;
            /* makes sure it stays on top */
        }

        #status {
            width: 200px;
            height: 200px;
            position: absolute;
            left: 50%;
            /* centers the loading animation horizontally one the screen */
            top: 50%;
            /* centers the loading animation vertically one the screen */
            background-image: url(https://raw.githubusercontent.com/niklausgerber/PreLoadMe/master/img/status.gif);
            /* path to your loading animation */
            background-repeat: no-repeat;
            background-position: center;
            margin: -100px 0 0 -100px;
            /* is width and height divided by two */

            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }



        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 0 auto 10px;
            }
            @page {
                size: A4 landscape;
                margin: 5mm 5mm 10mm 10mm;
                /* top, right,bottom last value was= 10, left */

            }

            a[href]:after {
                content: none !important;
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
        /*   End of Page Print Setting */


        .main__wrap {
            width: 98%;
            margin: 20px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
        }

        .table__part {
            display: flex;
        }

        table {
            width: 98%;
            padding: 5px;
        }

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid gray;
            padding: 8px;
            /* border-top: none;
            border-bottom: none; */
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        .td__sn {
            text-align: center;
            font-weight:normal;
        }
        .td__info {
            text-align: left;
            font-weight:normal;
            margin-left:5px;
        }
        .td_amount {
            text-align: right;
            font-weight:normal;
            margin-right:5px;
        }
        .td_total_amount {
            text-align: right;
            font-weight:bold;
            margin-right:5px;
        }
    </style>
    <!-- style -->
</head>

<body>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>


    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="date__part">
                <p> <b>Salary Month:{{ $monthName }}-{{ $year }} </b> </p>


            </div>
            <!-- title -->
            <div class="title__part">

                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
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
        <h3 style="text-align:center; color:red">InActive Employee Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <th rowspan="2">S.N</th>
                        <th rowspan="2">Project Name</th>
                        <th colspan="2">Basic </th>
                        <th colspan="2">Hourly </th>
                        <th colspan="2">Total</th>
                    </tr>
                    <tr>

                        <th>Employee</th>
                        <th>Amount</th>
                        <th> Employee</th>
                        <th> Amount</th>
                        <th>  Employee</th>
                        <th>  Amount</th>
                    </tr>
                    @php

                        $basic_total_amount = 0;
                        $basic_total_emp = 0;
                        $hourly_total_amount = 0;
                        $hourly_total_emp = 0;
                    @endphp

                    @foreach ($records as $ar)
                    @php
                        $basic_total_amount += ($ar['basic_salary']);
                        $basic_total_emp += $ar['basic_emp'];
                        $hourly_total_amount += ($ar['hourly_salary']);
                        $hourly_total_emp += $ar['hourly_emp'];


                    @endphp
                    <tr>
                        <td class="td__sn">{{ $loop->iteration }}</td>
                        <td class="td__info">{{$ar['proj_name'] }}</td>

                        <td class="td_amount">{{$ar['basic_emp'] }} </td>
                        <td class="td_amount">{{$ar['basic_salary'] }} </td>
                        <td class="td_amount">{{$ar['hourly_emp'] }} </td>
                        <td class="td_amount">{{$ar['hourly_salary'] }} </td>

                        <td class="td_amount">{{$ar['basic_emp'] + $ar['hourly_emp'] }} </td>
                        <td class="td_amount">{{$ar['basic_salary'] + $ar['hourly_salary']   }} </td>
                    </tr>
                    @endforeach
                    <tr style="background-color: yellow">
                        <td colspan="2" class="td_total_amount">Sub-total </td>
                        <td class="td_total_amount">{{ $basic_total_emp }} </td>
                        <td class="td_total_amount">{{number_format( round( $basic_total_amount) , 2, '.', ',')}} </td>

                         <td class="td_total_amount">{{ $hourly_total_emp }} </td>
                        <td class="td_total_amount">{{number_format( round( $hourly_total_amount) , 2, '.', ',')}} </td>

                         <td class="td_total_amount">{{ $basic_total_emp + $hourly_total_emp }} </td>
                        <td class="td_total_amount">{{number_format( round( $basic_total_amount+$hourly_total_amount) , 2, '.', ',')}} </td>
                    </tr>

                </tbody>
            </table>
        </section>
        <!-- ---------- -->


        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Approved By  </p>
                </div>
            </div>
        </section>

    </div>
</body>


</html>

<script>
    $(window).on('load', function() { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>
