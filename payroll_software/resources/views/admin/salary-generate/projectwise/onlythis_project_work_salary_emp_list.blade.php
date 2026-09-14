<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary

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
                margin: 15mm 0mm 10mm 0mm;
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
            width: 90%;
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
            width: 100%;
            padding: 5px;
        }

        table,
        tr {
            border: 2px solid black;
            border-collapse: collapse;
        }

        table th {
            font-size: 13px;
        }

        table td {
            text-align: left;
            font-size: 13px;

        }

        th,
        td {
            padding: 5px 2px;
            /* Top,Right,Bottom,left */
        }

        .td__left {

            text-align: left
        }

        .td__center {
            text-align: center
        }

        .td__right {
            text-align: right
        }

        .td__name {
            padding-bottom: 25px;
        }

        .td__bold {
            font-weight: 700;
        }

        .td__emplyoeeId {
            font-size: 14px;
            padding-bottom: 25px;
            color: blue;
            font-weight: 900;
            text-align: center
        }

        .td__sponser {
            color: green;
            font-weight: 300;
            text-align: center;

        }

        .sponser__name {
            font-size: 10px;
            font-weight: 100;

        }

        .country {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .employe__trade {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .td__project {
            font-size: 8px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }


        .td__multi__project {
            font-size: 10px;
            color: red;
            text-align: center
        }


        .td__red__color {
            color: red;
            text-align: center;
        }

        .td__total {
            font-weight: bold;
            text-align: center;
            color: black;
            font-size: 14px;
        }

        .box__signature {
            width: 200px;
            height: 30px;
            border: 1px solid gray;
            margin-top: 5px;
            margin-left: 0px;
            color: lightgray;
        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }


        /*#employeeinfo td,*/
        /*#employeeinfo th {*/
        /*    border: 2px solid #ddd;*/
        /*    padding: 8px;*/
        /*}*/

        /*#employeeinfo tr:nth-child(even) {*/
        /*    background-color: #f2f2f2;*/
        /*}*/

        /*#employeeinfo tr:hover {*/
        /*    background-color: #ddd;*/
        /*}*/

        /*#employeeinfo th {*/
        /*    padding-top: 5px;*/
        /*    padding-bottom: 5px;*/
        /*    text-align: center;*/
        /*    background-color: #EAEDED;*/
        /*    color: black;*/
        /*}*/

        /*#employeeinfo td {*/
        /*    padding-top: 5px;*/
        /*    padding-bottom: 5px;*/
            /*text-align: center;*/

        /*}*/


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
                <p> Salary Month : <strong> {{ $month }}, {{$year}} </strong> </p>
                  <p> <strong>Project:</strong> {{ $project}} </p>
            </div>
            <!-- title -->
            <div class="title__part">

                <!-- preloder -->
                <!-- <div class="loader"></div> -->
                <!-- preeloder -->


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
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th colspan ="9" class="td__header_title" >Salary of  {{ $month }}, {{$year}}</th>
                    </tr>
                    <tr>
                        <th>SL<br>No </th>
                        <th> Employee <br> ID </th>
                        <th> Employee Name </th>
                        <th> Iqama No<br> BS/Rate </th>
                        <th>Sponser <br>Hr/WrD </th>
                        <th>Nation <br> OT/Amt</th>
                        <th>Trade <br> (Food+Other) </th>
                        <th> <br> Total Salary </th>
                        <th> Project <br> Signature </th>
                    </tr>
                </thead>
                <tbody>

                    @php
                        $totalHours = 0;
                        $totalOverTimeHours = 0;
                        $totalOverTimeAmount = 0;
                        $totalFoodAllowance = 0;
                        $totalSaudiTax = 0;
                        $totalContribution = 0;
                        $totalOtherAdvance = 0;
                        $totalIqamaRenewal = 0;
                        $totalSalaryAmount = 0;
                        $grandTotalSalary = 0;
                    @endphp

                    @foreach($employees as $salary)

                    {{-- find multiple project --}}
                    @php
                        $totalHours += $salary->total_hour;
                        $totalOverTimeHours += $salary->total_overtime;
                        $totalOverTimeAmount += $salary->ot_amount;
                        $totalFoodAllowance += $salary->food_amount;
                        $totalSalaryAmount += $salary->total_amount;
                    @endphp

                    <tr style="border-bottom:0;">
                        <td class="td__center td__name">{{ $loop->iteration }}</td>
                        <td class="td__center td__emplyoeeId"> {{ $salary->employee_id }} </td>
                        <td class="td__left td__name"> <span>{{ $salary->employee_name }}</span> </td>

                        <td class="td__center"> {{ $salary->akama_no }} <br><br> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}

                        </td>
                        <td class="td__sponser"> <span class="sponser__name">{{Str::limit( $salary->spons_name,15)}}</span> <br><br> {{ $salary->total_hour }}/{{ $salary->total_day }} </td>
                        <td class="td__center"> <span class="country"> {{ Str::limit($salary->country_name,3) }} </span> <br><br>{{ $salary->total_overtime }}/{{ $salary->ot_amount }}</td>

                        <td class="td__center"> <span class="employe__trade"> {{ Str::limit($salary->catg_name,10) }} </span> <br><br> {{round($salary->food_amount) }}
                            + {{ $salary->other_amount }} </td>
                        <td class="td__gross_salary">  <br><br>
                            {{ round( $salary->total_amount) }} </td>
                        <td class="td__project"> {{ Str::limit($salary->proj_name,40) }}<br>
                                 {{-- <div class="box__signature">     --}}
                        </div>
                        </td>
                    </tr>

                    @endforeach


                    {{-- match with table border color and hide column --}}
                    <tr style="background-color: #ddd;">

                        <td class="td__total" colspan="5">Total <br><br> {{ "" }}</td>
                        <td class="td__total"> O.T+Amnt <br><br>{{$totalOverTimeHours}} / {{ round($totalOverTimeAmount) }}</td>
                        <td class="td__total">Food Allo. <br><br> {{$totalFoodAllowance }}</td>
                        <td class="td__total">Gross Salary <br><br>  {{round( $totalSalaryAmount) }} </td>
                        <td class="td__project"> {{""}} <br> {{""}} </td>
                    </tr>


                </tbody>
            </table>
        </section>
        <!-- ---------- -->


        </section>
        {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
        <section>
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
