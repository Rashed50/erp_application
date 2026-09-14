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

        #employeeinfo th,
        #employeeinfo td {
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
            padding-top: 15px;
            padding-bottom: 15px;
            text-align: center;
            background-color: #2878B9;
            color: black;
            font-size: 14px;
            font-weight: bold;
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
                <p>   <strong class="td__red__color">  </strong> </p>


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
        <h3 style="text-align:center; color:red">{{ $report_title[0] }} </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Jan-{{substr( $year, 2) }}</th>
                        <th>Feb-{{substr( $year, 2) }}</th>
                        <th>Mar-{{substr( $year, 2) }}</th>
                        <th>Apr-{{substr( $year, 2) }}</th>
                        <th>May-{{substr( $year, 2) }}</th>
                        <th>Jun-{{substr( $year, 2) }}</th>
                        <th>Jul-{{substr( $year, 2) }}</th>
                        <th>Aug-{{substr( $year, 2) }}</th>
                        <th>Sep-{{substr( $year, 2) }}</th>
                        <th>Oct-{{substr( $year, 2) }}</th>
                        <th>Nov-{{substr( $year, 2) }}</th>
                        <th>Dec-{{substr( $year, 2) }}</th>
                        <th>Total</th>
                    </tr>
                    @foreach ($records as $arecord )
                    @php
                        $summary = $arecord->summary;
                        $month_id = 1;
                        $total_emp = 0;
                        $total_salary = 0;
                        $counter = 0;
                    @endphp
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{ $arecord->proj_name }}</td>
                        @for ($month_id = 1; $month_id <= 12; $month_id++)

                            @if( $summary->pluck('month')->contains($month_id))
                                @php
                                    $emp = $summary[$counter]->total_emp ?? 0;
                                    $total_emp += $emp;
                                    $salary = $summary[$counter]->total_salary ?? 0;
                                    $total_salary += $salary;
                                    $counter++;

                                @endphp

                                <td class="td_amount">
                                     {{ number_format(round($salary) ,0, ',') }}
                                </td>
                            @else
                                <td class="td_amount">
                                    -
                                </td>
                             @endif
                        @endfor
                        <td  class="td_amount">{{  number_format(round($total_salary),0, ',') }}</td>
                    </tr>
                    @endforeach
                 </tbody>
            </table>
        </section>
        <!-- ---------- -->


        <section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
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

<script>
    $(window).on('load', function() { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>
