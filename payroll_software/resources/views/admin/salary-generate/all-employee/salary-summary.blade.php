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
            border: 1px solid #ddd;
            padding: 8px;
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
                <p> Salary Month of <br>{{ $monthName }}, {{ $year }}  </p>


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
        <h3 style="text-align:center; color:red">Projet base Paid Unpaid Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>

                    <tr>
                        <th rowspan="2">S.N</th>
                        <th rowspan="2">Project</th>
                        <th colspan="6">Unpaid Salary</th>
                        <th colspan="6">Paid Salary</th>
                        <th colspan="6">Total Salary</th>
                    </tr>
                    <tr style="border-bottom:0;">
                        <td class="td__sn">Emp.</td>
                        <td class="td_amount">Gross </td>
                        <td class="td_amount"> S.T</td>
                        <td class="td_amount"> ID Renew </td>
                        <td class="td_amount">  Other</td>
                        <td class="td_amount">Total Salary</td>

                        <td class="td_amount">Emp.</td>
                        <td class="td_amount">Gross </td>
                        <td class="td_amount"> S.T</td>
                        <td class="td_amount"> ID Renew </td>
                        <td class="td_amount"> Other</td>
                        <td class="td_amount">Total Salary</td>

                        <td class="td_amount">Emp.</td>
                        <td class="td_amount">Gross </td>
                        <td class="td_amount"> S.T</td>
                        <td class="td_amount"> ID Renew </td>
                        <td class="td_amount"> Other</td>
                        <td class="td_amount">Total Salary</td>
                    </tr>


                    @foreach ($records as $arecord)
                        <tr style="border-bottom:0;">
                            <td class="td__sn">{{ $loop->iteration }}</td>
                            <th class="td__info">{{ $arecord->proj_name }}</th>

                            <td class="td__sn">{{$arecord->unpaid_emp}}</td>
                            <td class="td__info">{{$arecord->unpaid_salary}}</td>
                            <td class="td__info">{{$arecord->unpaid_saudi_tax}}</td>
                            <td class="td__info">{{$arecord->unpaid_iqama_adv}}</td>
                            <td class="td__info">{{ $arecord->unpaid_other_adv}}</td>
                            <td class="td__info">{{$arecord->unpaid_salary + ($arecord->unpaid_saudi_tax + $arecord->unpaid_iqama_adv + $arecord->unpaid_other_adv)}}</td>


                            <td class="td__info">{{$arecord->paid_emp}}</td>
                            <td class="td__info">{{$arecord->paid_salary}}</td>
                            <td class="td__info">{{$arecord->paid_saudi_tax}}</td>
                            <td class="td__info">{{$arecord->paid_iqama_adv}}</td>
                            <td class="td__info">{{$arecord->paid_other_adv}}</td>
                            <td class="td__info">{{$arecord->paid_salary + ($arecord->paid_saudi_tax + $arecord->paid_iqama_adv + $arecord->paid_other_adv)}}</td>


                            <td class="td__info">{{$arecord->unpaid_emp + $arecord->paid_emp}}</td>
                            <td class="td__info">{{$arecord->unpaid_salary + $arecord->paid_salary}}</td>
                            <td class="td__info">{{$arecord->unpaid_saudi_tax + $arecord->paid_saudi_tax}}</td>
                            <td class="td__info">{{$arecord->unpaid_iqama_adv + $arecord->paid_iqama_adv}}</td>
                            <td class="td__info">{{ $arecord->unpaid_other_adv + $arecord->paid_other_adv}}</td>
                            <td class="td__info">{{$arecord->unpaid_salary + $arecord->paid_salary +
                            ($arecord->unpaid_saudi_tax + $arecord->paid_saudi_tax + $arecord->unpaid_iqama_adv + $arecord->paid_iqama_adv + $arecord->unpaid_other_adv + $arecord->paid_other_adv)}}</td>

                        </tr>
                    @endforeach

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
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
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
