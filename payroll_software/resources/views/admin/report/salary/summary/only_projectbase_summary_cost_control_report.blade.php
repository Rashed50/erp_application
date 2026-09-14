<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hours&Salary Summary
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
                max-width: 95%;
                margin: 0 auto 10px;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 5mm 10mm 0mm;
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

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo th {
            border: 1px solid #ddd;
            padding-top: 8px;
            padding-bottom: 8px;

        }

        #employeeinfo td {
            border: 1px solid #ddd;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }
        .td__amount {
            text-align: right;
            margin-right:5px;
            padding-right: 5px;
        }

        .td__total {
            text-align: right;
            font-weight:bold;
            margin-right:5px;
            padding-right: 5px;
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
        <br>  <h3 style="text-align:center; color:black"> Project Base Man-Hours & Salary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                 <thead  style=" background-color:#2878B9; color:black; font-weight:bold;">
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Month <br>Year</th>
                        <th> Employees</th>
                        <th> Over Time</th>
                        <th> Basic Hours</th>
                        <th>Total Hours</th>
                        <th>Total Salary</th>
                    </tr>
                </thead>

                <tbody>

                    @php
                    $total_emp = 0;
                    $total_basic_hour = 0;
                    $total_overtime = 0;
                    $total_salary = 0;
                    @endphp
                    @foreach($records as $arecord)
                        @php
                            $total_emp += $arecord['total_emp'];
                            $total_basic_hour +=   $arecord['total_hour'];
                            $total_overtime += $arecord['total_overtime'];
                            $total_salary += $arecord['total_salary'];
                        @endphp
                    <tr>
                        <td style="text-align: center">{{$loop->iteration  }}</td>
                        <td  style="text-align: left; padding-left:3px;">{{$arecord['project_name']}}</td>
                        <td style="text-align: center" >{{Str::substr($arecord['month_name'], 0, 3)}} {{ Str::substr($arecord['year'], 2, 2) }}</td>
                        <td style="text-align: center" >{{ $arecord['total_emp'] }}</td>
                        <td  class="td__amount">{{ number_format(  $arecord['total_overtime'],2)}}</td>
                        <td  class="td__amount"   >{{number_format(  $arecord['total_hour'],2) }}</td>
                        <td  class="td__amount"   >{{ number_format( $arecord['total_hour'] + $arecord['total_overtime'] ,2)}}</td>
                        <td class="td__amount"  >{{number_format( $arecord['total_salary'],2) }}</td>
                    </tr>
                    @endforeach
                    <tfoot  style=" background-color:#C5AB57EE; color:black; font-weight:bold;">
                        <tr>
                            <td colspan="3" style="text-align: center;font-weight:bold" >  Total </td>
                            <td style="text-align: center" >{{  $total_emp }}</td>
                            <td  class="td__total"  >{{number_format(  $total_overtime,2) }}</td>
                            <td  class="td__total"   >{{number_format(  $total_basic_hour,2) }}</td>
                            <td  class="td__total"   >{{number_format(  $total_basic_hour + $total_overtime,2) }}</td>
                            <td class="td__total"  >{{ number_format( $total_salary,2) }}</td>
                        </tr>
                    </tfoot>
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
            {{-- <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p> --}}
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
