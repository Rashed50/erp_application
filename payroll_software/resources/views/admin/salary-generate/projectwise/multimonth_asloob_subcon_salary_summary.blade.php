<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Sum. Report
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
                max-width: 100%;
                margin: 0 auto 0px;
            }
            @page {
                size: A4 landscape;
                margin: 5mm 10mm 5mm 10mm;
                /* top, right,bottom, left */

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
            font-size:12px;


        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #000;
            padding: 5px;
            word-wrap: normal;
        }

        /* #employeeinfo tr:nth-child(even) {
            background-color:#E5E7E9;
        } */

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            /* background-color: #B3E6FA; */
            color: black;
        }
        .th_header_section{
            /* background-color: #FBD2AF;  */
            text-align: center;
            font-weight:bold;
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
            width: 200px;
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
            background-color: #f0f0f0;
        }
        .td_subtotal {
            text-align: right;
            font-weight:bold;
            margin-right:5px;
            background-color: #e8e8e8;
        }
        .td_subtotal_label {
            text-align: right;
            font-weight:bold;
            background-color: #e8e8e8;
            padding-right: 10px;
        }

        .subtotal_row {
            font-weight: bold;
            background-color: #D5D8DC;
            border-top: 2px solid #000;
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
        <h3 style="text-align:center; color:red">Multi-Month Asloob and Subcon Sponsor Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <colgroup>
                        <col span="2" style="background-color: #AED6F1"  >
                        <col span="3" style="background-color: #F9E79F"  >
                        <col span="2" style="background-color: #F5B7B1"  >
                        <col span="2" style="background-color: #AED6F1"  >
                    </colgroup>

                    <tr>
                        <th colspan="9" class="th_header_section"> Asloob All Sponsor Multiple Month Salary Summary</th>
                    </tr>

                    <tr>
                        <th>S.N</th>
                        <th>Month</th>
                        <th>Total Epm.</th>
                        <th>Gross <br> Salary</th>
                        <th>Net<br> Salary</th>
                        <th>Paid Emp.</th>
                        <th>Paid<br> Salary</th>
                        <th>Unpaid Emp.</th>
                        <th>Unpaid<br> Salary</th>

                    </tr>


                    @php
                        $subtotal_all_included = 0;
                        $subtotal_total_emp = 0;
                        $subtotal_net_salary = 0;
                        $subtotal_paid_emp = 0;
                        $subtotal_paid_salary = 0;
                        $subtotal_unpaid_emp = 0;
                        $subtotal_unpaid_salary = 0;
                    @endphp

                    @foreach($asloob_result as $apr)

                        @php
                            $all_included = $apr->all_incl_total_paid_salary;
                            $total_emp = $apr->total_unpaid_emp + $apr->total_paid_emp;
                            $net_salary = $apr->total_unpaid_net_salary + $apr->total_paid_net_salary;
                            $paid_emp = $apr->total_paid_emp;
                            $paid_salary = $apr->total_paid_net_salary;
                            $unpaid_emp = $apr->total_unpaid_emp;
                            $unpaid_salary = $apr->total_unpaid_net_salary;

                            // Subtotal
                            $subtotal_all_included += $all_included;
                            $subtotal_total_emp += $total_emp;
                            $subtotal_net_salary += $net_salary;
                            $subtotal_paid_emp += $paid_emp;
                            $subtotal_paid_salary += $paid_salary;
                            $subtotal_unpaid_emp += $unpaid_emp;
                            $subtotal_unpaid_salary += $unpaid_salary;
                        @endphp
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{ date('F', mktime(0, 0, 0, $apr->month, 1)) }} {{$apr->year }}</td>
                            <td class="td_amount">{{ number_format($apr->total_unpaid_emp+$apr->total_paid_emp,0) }}</td>
                            <td class="td_amount">{{ number_format(( $apr->all_incl_total_paid_salary+$apr->all_incl_total_paid_salary) , 0) }}</td>

                            <td class="td_amount">{{ number_format(round($apr->total_unpaid_net_salary+$apr->total_paid_net_salary, 2), 0, '.', ',') }}</td>

                            <td class="td_amount">{{ number_format($apr->total_paid_emp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($apr->total_paid_net_salary, 2), 0, '.', ',') }}</td>
                            <td class="td_amount">{{ number_format($apr->total_unpaid_emp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($apr->total_unpaid_net_salary, 2), 0, '.', ',') }}</td>

                    </tr>
                    @endforeach
                     {{-- SUBTOTAL --}}
                    <tr class="subtotal_row">
                        <th colspan="2" class="td__info">
                            SUBTOTAL
                        </th>

                        <th class="td_amount">
                            {{ number_format($subtotal_total_emp, 0, '.', ',') }}
                        </th>
                        <th class="td_amount">
                            {{ number_format($subtotal_all_included, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_net_salary, 2), 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format($subtotal_paid_emp, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_paid_salary, 2), 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format($subtotal_unpaid_emp, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_unpaid_salary, 2), 0, '.', ',') }}
                        </th>
                    </tr>




                </tbody>
            </table>
        </section>
        <br><br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <colgroup>
                        <col span="2" style="background-color: #AED6F1"  >
                        <col span="3" style="background-color: #F9E79F"  >
                        <col span="2" style="background-color: #F5B7B1"  >
                        <col span="2" style="background-color: #AED6F1"  >
                    </colgroup>

                    <tr>
                        <th colspan="9" class="th_header_section"> Subcontract Sponsor Multiple Month Salary Summary</th>
                    </tr>

                    <tr>
                        <th>S.N</th>
                        <th>Month</th>

                        <th>Total Epm.</th>
                        <th>Gross <br> Salary</th>
                        <th>Net<br> Salary</th>
                        <th>Paid Emp.</th>
                        <th>Paid<br> Salary</th>
                        <th>Unpaid Emp.</th>
                        <th>Unpaid<br> Salary</th>

                    </tr>


                    @php
                        $subtotal_all_included = 0;
                        $subtotal_total_emp = 0;
                        $subtotal_net_salary = 0;
                        $subtotal_paid_emp = 0;
                        $subtotal_paid_salary = 0;
                        $subtotal_unpaid_emp = 0;
                        $subtotal_unpaid_salary = 0;
                    @endphp

                    @foreach($subcon_result as $apr)

                        @php
                            $all_included = $apr->all_incl_total_paid_salary;
                            $total_emp = $apr->total_unpaid_emp + $apr->total_paid_emp;
                            $net_salary = $apr->total_unpaid_net_salary + $apr->total_paid_net_salary;
                            $paid_emp = $apr->total_paid_emp;
                            $paid_salary = $apr->total_paid_net_salary;
                            $unpaid_emp = $apr->total_unpaid_emp;
                            $unpaid_salary = $apr->total_unpaid_net_salary;

                            // Subtotal
                            $subtotal_all_included += $all_included;
                            $subtotal_total_emp += $total_emp;
                            $subtotal_net_salary += $net_salary;
                            $subtotal_paid_emp += $paid_emp;
                            $subtotal_paid_salary += $paid_salary;
                            $subtotal_unpaid_emp += $unpaid_emp;
                            $subtotal_unpaid_salary += $unpaid_salary;
                        @endphp
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{ date('F', mktime(0, 0, 0, $apr->month, 1)) }} {{$apr->year }}</td>
                            <td class="td_amount">{{ number_format($apr->total_unpaid_emp+$apr->total_paid_emp,0) }}</td>
                            <td class="td_amount">{{ number_format(( $apr->all_incl_total_paid_salary+$apr->all_incl_total_paid_salary) , 0) }}</td>
                            <td class="td_amount">{{ number_format(round($apr->total_unpaid_net_salary+$apr->total_paid_net_salary, 2), 0, '.', ',') }}</td>

                            <td class="td_amount">{{ number_format($apr->total_paid_emp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($apr->total_paid_net_salary, 2), 0, '.', ',') }}</td>
                            <td class="td_amount">{{ number_format($apr->total_unpaid_emp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($apr->total_unpaid_net_salary, 2), 0, '.', ',') }}</td>

                    </tr>
                    @endforeach
                     {{-- SUBTOTAL --}}
                    <tr class="subtotal_row">
                        <th colspan="2" class="td__info">
                            SUBTOTAL
                        </th>
                        <th class="td_amount">
                            {{ number_format($subtotal_total_emp, 0, '.', ',') }}
                        </th>
                        <th class="td_amount">
                            {{ number_format($subtotal_all_included, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_net_salary, 2), 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format($subtotal_paid_emp, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_paid_salary, 2), 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format($subtotal_unpaid_emp, 0, '.', ',') }}
                        </th>

                        <th class="td_amount">
                            {{ number_format(round($subtotal_unpaid_salary, 2), 0, '.', ',') }}
                        </th>
                    </tr>




                </tbody>
            </table>
        </section>


        <!-- ---------- -->


        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
        </section>

    </div>
</body>

<script>
    $(window).on('load', function() { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>

</html>
