<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Salary Statement</title>
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
                margin: 0 auto 10px;
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
            width: 100%;
            padding: 2px;
        }

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 0.5px solid #ddd;
            padding:5px;
            font-size: 12px;
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
            background-color: #C5AB57EE ;
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
            padding-left:2px;
            text-align: left;
        }
        .td__amount {

            text-align: right;
            font-weight:normal;
            margin-right:2px;

        }
        .td__total {

            text-align: right;
            font-weight:bold;
            padding-right:2px;

        }
         .td__groundtotal {

            text-align: right;
            font-weight:bold;
            padding-top:15px;
            padding-bottom:15px;
            padding-right:2px;
            height: 30px;


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
        <br>  <h3 style="text-align:left; color:red">Yearly Salary Statement-{{ $year }} </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                 <thead>
                                        <tr>
                        <th rowspan="2">S.N</th>
                        <th rowspan="2">Project</th>
                        <th style="padding-top: 3px;"> Jan  </th>
                        <th style="padding-top: 3px;"> Feb  </th>
                        <th style="padding-top: 3px;"> Mar  </th>
                        <th style="padding-top: 3px;"> Apr  </th>
                        <th style="padding-top: 3px;"> May  </th>
                        <th style="padding-top: 3px;"> Jun  </th>
                        <th style="padding-top: 3px;"> Jul  </th>
                        <th style="padding-top: 3px;"> Aug  </th>
                        <th style="padding-top: 3px;"> Sep  </th>
                        <th style="padding-top: 3px;"> Oct  </th>
                        <th style="padding-top: 3px;"> Nov  </th>
                        <th style="padding-top: 3px;"> Dec  </th>
                        <th rowspan="2">Total</th>
                    </tr>
                     <tr>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>

                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>

                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                        <th style="padding-top: 3px;"> {{ $year }}  </th>
                    </tr>
                </thead>
            <tbody>
                     

                    @php
                    $monthbase_total = array_fill(0,12,0);
                    $all_total_amount = 0;
                    @endphp

                    @foreach($final_records as $ar)
                    @php
                      $records = $ar->salary_record;
                      $project_total_amount = 0;
                      $counter = 0;
                    @endphp
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__project">{{$ar->proj_name }}</td>
                        @foreach($records as $ar)
                        @php
                            $project_total_amount += $ar;
                            $monthbase_total[$counter++] += $ar;
                        @endphp
                            <td class="td__amount">{{ $ar <= 0 ? '-': number_format( $ar , 2, '.', ',') }} </td>
                        @endforeach
                        <td class="td__total">{{ $project_total_amount <= 0 ? '-':  number_format( $project_total_amount , 2, '.', ',') }} </td>

                    </tr>
                    @endforeach
                </tbody>

                <tr style="background-color:lightgray;">
                        <td colspan="2" class="td__total"> Sub-Total</td>

                        @foreach($monthbase_total as $ar)
                        @php
                             $all_total_amount  +=  $ar ;
                        @endphp
                            <td class="td__groundtotal">{{ $ar <= 0 ? '-': number_format( $ar , 2, '.', ',') }} </td>
                        @endforeach
                        <td class="td__groundtotal">{{ $all_total_amount <= 0 ? '-':  number_format( $all_total_amount , 2, '.', ',') }} </td>
                </tr>

            </table>
        </section>
        <!-- ---------- -->
        <section>
        {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> </b>  Prepared By <br> <b>   {{$login_name}}  </b>  </p>
                    <p>  <b>  -------------------- <br>   </b> Verified By <br> <b>Accounts/HR</b> </p>
                    <p>  <b>  ----------------------- <br>  </b> Approved By <br> <b>CEO</b>  </p>
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
