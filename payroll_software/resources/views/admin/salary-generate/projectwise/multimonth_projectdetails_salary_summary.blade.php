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
        <h3 style="text-align:center; color:red">Multi-Month Project Details Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>

                    <colgroup>
                        <col span="2" style="background-color: lightgray"  >
                        @php
                            $cololor_code = ['#D7BDE2', '#AED6F1', '#F9E79F', '#A3E4D7', '#F5B7B1', '#D2B4DE', '#A9CCE3', '#FAD7A0', '#A2D9CE', '#F5CBA7'];
                            $counter = 0;
                        @endphp
                        @foreach ($my_list as $my)
                            <col span="6" style="background-color:{!! $cololor_code[$counter++] !!}" >
                        @endforeach

                    </colgroup>
                    <tr>
                        <th colspan="2" class="th_header_section"> </th>
                        @foreach ($my_list as $my)
                            <th colspan="6" class="th_header_section">{{ date('F', mktime(0, 0, 0, $my['month'], 1)) }}, {{ $my['year'] }}</th>
                        @endforeach
                    </tr>

                    <tr>
                        <th>S.N</th>
                        <th>Project</th>
                        @foreach ($my_list as $my)
                            <th>Basic <br> Emp.</th>
                            <th>Basic<br> Salary</th>
                            <th>Hourly <br> Emp.</th>
                            <th>Hourly<br> Salary</th>
                            <th>Total <br> Emp.</th>
                            <th>Total<br> Salary</th>
                        @endforeach
                    </tr>

                    @php
                        // Initialize arrays for subtotals
                        $subtotals = [];
                        foreach ($my_list as $index => $my) {
                            $subtotals[$index] = [
                                'basic_emp' => 0,
                                'basic_salary' => 0,
                                'hourly_emp' => 0,
                                'hourly_salary' => 0,
                                'total_emp' => 0,
                                'total_salary' => 0
                            ];
                        }
                    @endphp

                    @foreach($project_list as $apr)
                    @php
                        $salary_records = $apr->salary_records;
                    @endphp
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{$apr->proj_name }}</td>

                        @foreach ($salary_records as $index => $ar)
                            @php
                                // Calculate values
                                $basicEmp = $ar->basic_emp ?? 0;
                                $hourlyEmp = $ar->hourly_emp ?? 0;
                                $basicSalary = $ar->all_incl_total_basic_salary ?? 0;
                                $hourlySalary = $ar->all_incl_total_hourly_salary ?? 0;
                                $totalEmp = $basicEmp + $hourlyEmp;
                                $totalSalary = $basicSalary + $hourlySalary;

                                // Add to subtotals
                                $subtotals[$index]['basic_emp'] += $basicEmp;
                                $subtotals[$index]['basic_salary'] += $basicSalary;
                                $subtotals[$index]['hourly_emp'] += $hourlyEmp;
                                $subtotals[$index]['hourly_salary'] += $hourlySalary;
                                $subtotals[$index]['total_emp'] += $totalEmp;
                                $subtotals[$index]['total_salary'] += $totalSalary;
                            @endphp
                            <td class="td_amount">{{ number_format($basicEmp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($basicSalary, 2), 0, '.', ',') }}</td>
                            <td class="td_amount">{{ number_format($hourlyEmp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($hourlySalary, 2), 0, '.', ',') }}</td>
                            <td class="td_amount">{{ number_format($totalEmp, 0) }}</td>
                            <td class="td_amount">{{ number_format(round($totalSalary, 2), 0, '.', ',') }}</td>
                        @endforeach
                    </tr>
                    @endforeach

                    <!-- Subtotal Row -->
                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                        <td colspan="2" class="td_subtotal_label">SUB TOTAL</td>
                        @foreach ($subtotals as $subtotal)
                            <td class="td_subtotal">{{ number_format($subtotal['basic_emp'], 0) }}</td>
                            <td class="td_subtotal">{{ number_format(round($subtotal['basic_salary'], 2), 0, '.', ',') }}</td>
                            <td class="td_subtotal">{{ number_format($subtotal['hourly_emp'], 0) }}</td>
                            <td class="td_subtotal">{{ number_format(round($subtotal['hourly_salary'], 2), 0, '.', ',') }}</td>
                            <td class="td_subtotal">{{ number_format($subtotal['total_emp'], 0) }}</td>
                            <td class="td_subtotal">{{ number_format(round($subtotal['total_salary'], 2), 0, '.', ',') }}</td>
                        @endforeach
                    </tr>

                    <!-- Grand Total Row -->
                    @php
                        $grandTotal = [
                            'basic_emp' => 0,
                            'basic_salary' => 0,
                            'hourly_emp' => 0,
                            'hourly_salary' => 0,
                            'total_emp' => 0,
                            'total_salary' => 0
                        ];
                        foreach ($subtotals as $subtotal) {
                            $grandTotal['basic_emp'] += $subtotal['basic_emp'];
                            $grandTotal['basic_salary'] += $subtotal['basic_salary'];
                            $grandTotal['hourly_emp'] += $subtotal['hourly_emp'];
                            $grandTotal['hourly_salary'] += $subtotal['hourly_salary'];
                            $grandTotal['total_emp'] += $subtotal['total_emp'];
                            $grandTotal['total_salary'] += $subtotal['total_salary'];
                        }
                    @endphp
                    <tr style="background-color: #d4d4d4; font-weight: bold;">
                        <td colspan="2" class="td_total_amount" style="text-align: center; background-color: #d4d4d4;">GRAND TOTAL</td>
                        @foreach ($subtotals as $subtotal)
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format($grandTotal['basic_emp'], 0) }}</td>
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format(round($grandTotal['basic_salary'], 2), 0, '.', ',') }}</td>
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format($grandTotal['hourly_emp'], 0) }}</td>
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format(round($grandTotal['hourly_salary'], 2), 0, '.', ',') }}</td>
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format($grandTotal['total_emp'], 0) }}</td>
                            <td class="td_total_amount" style="background-color: #d4d4d4;">{{ number_format(round($grandTotal['total_salary'], 2), 0, '.', ',') }}</td>
                        @endforeach
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
