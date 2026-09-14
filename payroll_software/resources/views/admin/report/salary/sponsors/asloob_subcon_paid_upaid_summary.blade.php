<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Subcon & Asloob Salary Summary</title>
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
                max-width: 90%;
                margin:  auto 10px;
            }
            @page {
                size: A4 landscape;
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

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid black;
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

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
            /*text-align: center;*/

        }
        .td__sn{
             text-align: center;
        }
         .td__project{
            padding-left:5px;
            text-align: left;
        }
        .td__amount {

            text-align: right;
            font-weight:normal;
            margin-right:5px;

        }
        .td__total {

            text-align: right;
            font-weight:bold;
            margin-right:5px;

        }


        .yellow_header{
             text-align: left;
            font-weight:300;
            background:#f4f5c3;
            color: black
         }


        .blue_header{   text-align: center;
            font-weight:300;
            background:#ace8e9;
            color: black
         }

        .brown_header{   text-align: center;
            font-weight:300;
            background: #dfbeb6;
            color: black
         }

        .green_header{   text-align: center;
            font-weight:300;
            background: #aee9bc;
            color: black
         }
     .tr__ground_total{
            text-align: right;
            font-weight:bold;
            margin-right:5px;
            background: lightgray;
            color: black
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
               <p> <strong>Print </strong> {{ Carbon\Carbon::now() }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <br>  <h3 style="text-align:center; color:black"> Subcontractor & Asloob Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
            <tbody>
                    <tr >
                        <td rowspan="2" class="yellow_header" style="text-align: center; font-weight:bold">S.N</td>
                        <td rowspan="2"  class="yellow_header" style="text-align: center; font-weight:bold">Project Name </td>
                        <td rowspan="2" class="yellow_header" style="text-align: center; font-weight:bold">Month </td>
                        <td colspan="3" class="blue_header" style="text-align: center; font-weight:bold">Sub-Contractor </td>

                        <td colspan="3" class="brown_header" style="text-align: center; font-weight:bold">Staff  </td>
                        <td colspan="3" class="green_header" style="text-align: center; font-weight:bold">Workmen  </td>
                    </tr>

                    <tr>
                        <td  class="blue_header" style="text-align: center; font-weight:bold">Total Salary  </td>
                        <td  class="blue_header" style="text-align: center; font-weight:bold">Paid</td>
                        <td  class="blue_header">Unpaid</td>

                         <td  class="brown_header" style="text-align: center; font-weight:bold">Total Salary  </td>
                        <td  class="brown_header" style="text-align: center; font-weight:bold">Paid</td>
                        <td  class="brown_header" style="text-align: center; font-weight:bold">Unpaid</td>

                        <td  class="green_header" style="text-align: center; font-weight:bold">Total Salary  </td>
                        <td  class="green_header" style="text-align: center; font-weight:bold">Paid</td>
                        <td  class="green_header" style="text-align: center; font-weight:bold">Unpaid</td>
                      </tr>

                    @php


                      $subcon_unpaid_grand_total = 0;
                      $subcon_paid_grand_total = 0;

                      $asloob_paid_grand_total = 0;
                      $asloob_unpaid_grand_total = 0;
                      $paid_grand_total = 0;
                      $unpaid_grand_total = 0;
                      $grand_total_amount = 0;



                     @endphp

                    @foreach($summary_records as $ar)
                    @php

                    $aproject_paid_total =0;
                     $aproject_unpaid_total = 0;

                    @endphp
                    <tr>
                        <td class="yellow_header"> {{$loop->iteration}}</td>
                        <td class="yellow_header">{{$ar->proj_name }}</td>

                        @php

                        $subcon_unpaid_grand_total += $ar->subcon_unpaid_salary;
                        $subcon_paid_grand_total += $ar->subcon_paid_salary;

                        $asloob_paid_grand_total += $ar->asloob_paid_salary;
                        $asloob_unpaid_grand_total += $ar->asloob_unpaid_salary;

                        $total_employee = $ar->subcon_total_emp+$ar->asloob_total_emp;
                        $aproject_paid_total +=  $ar->subcon_paid_salary+$ar->asloob_paid_salary;
                        $aproject_unpaid_total +=  $ar->subcon_unpaid_salary+$ar->asloob_unpaid_salary;



                        @endphp
                          <td class="yellow_header">{{
                             Str::limit(date("F", mktime(0, 0, 0, $month , 10)), 3,'');
                        }} {{ substr($year ,-2)}}</td>
                            <td class="blue_header">  {{ $ar->subcon_unpaid_salary +  $ar->subcon_paid_salary <= 0 ? '-': number_format( $ar->subcon_unpaid_salary+ $ar->subcon_paid_salary , 2, '.', ',') }} </td>
                            <td class="blue_header">  {{ $ar->subcon_paid_salary <= 0 ? '-': number_format( $ar->subcon_paid_salary , 2, '.', ',') }} </td>
                            <td class="blue_header">  {{ $ar->subcon_unpaid_salary <= 0 ? '-': number_format( $ar->subcon_unpaid_salary , 2, '.', ',') }} </td>


                            <td class="brown_header">  {{ $ar->asloob_paid_salary +  $ar->asloob_unpaid_salary <= 0 ? '-': number_format( $ar->asloob_paid_salary+ $ar->asloob_unpaid_salary , 2, '.', ',') }} </td>
                            <td class="brown_header">  {{ $ar->asloob_paid_salary <= 0 ? '-': number_format( $ar->asloob_paid_salary , 2, '.', ',') }} </td>
                            <td class="brown_header">  {{ $ar->asloob_unpaid_salary <= 0 ? '-': number_format( $ar->asloob_unpaid_salary , 2, '.', ',') }} </td>

                            <td class="green_header">  {{ $aproject_paid_total+$aproject_unpaid_total <= 0 ? '-': number_format( $aproject_paid_total+$aproject_unpaid_total , 2, '.', ',') }} </td>
                            <td class="green_header">  {{ $aproject_paid_total <= 0 ? '-': number_format( $aproject_paid_total , 2, '.', ',') }} </td>
                            <td class="green_header">  {{ $aproject_unpaid_total <= 0 ? '-': number_format( $aproject_unpaid_total , 2, '.', ',') }} </td>
                    </tr>
                    @endforeach
                    <tr class="tr__ground_total">

                        <td colspan="3" >TOTAL</td>

                            <td class="tr__ground_total">{{ $subcon_unpaid_grand_total+$subcon_paid_grand_total <= 0 ? '-':  number_format( $subcon_unpaid_grand_total+$subcon_paid_grand_total , 2, '.', ',') }}  </td>
                            <td class="tr__ground_total">{{ $subcon_paid_grand_total <= 0 ? '-':  number_format( $subcon_paid_grand_total , 2, '.', ',') }}  </td>
                            <td  class="tr__ground_total">{{ $subcon_unpaid_grand_total <= 0 ? '-':  number_format( $subcon_unpaid_grand_total , 2, '.', ',') }}  </td>

                            <td  class="tr__ground_total">{{ $asloob_paid_grand_total+ $asloob_unpaid_grand_total <= 0 ? '-':  number_format( $asloob_paid_grand_total+ $asloob_unpaid_grand_total , 2, '.', ',') }}  </td>
                            <td  class="tr__ground_total">{{ $asloob_paid_grand_total <= 0 ? '-':  number_format( $asloob_paid_grand_total , 2, '.', ',') }}  </td>
                            <td  class="tr__ground_total">{{ $asloob_unpaid_grand_total <= 0 ? '-':  number_format( $asloob_unpaid_grand_total , 2, '.', ',') }}  </td>

                            <td  class="tr__ground_total">{{ $subcon_unpaid_grand_total+$subcon_paid_grand_total+$asloob_paid_grand_total+ $asloob_unpaid_grand_total <= 0 ? '-':  number_format( $subcon_unpaid_grand_total+$subcon_paid_grand_total+$asloob_paid_grand_total+ $asloob_unpaid_grand_total , 2, '.', ',') }}  </td>
                            <td  class="tr__ground_total">{{ $asloob_paid_grand_total+ $subcon_paid_grand_total <= 0 ? '-':  number_format( $asloob_paid_grand_total+ $subcon_paid_grand_total , 2, '.', ',') }}  </td>
                            <td  class="tr__ground_total">{{ $subcon_unpaid_grand_total+$asloob_unpaid_grand_total <= 0 ? '-':  number_format( $subcon_unpaid_grand_total+$asloob_unpaid_grand_total , 2, '.', ',') }}  </td>

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
