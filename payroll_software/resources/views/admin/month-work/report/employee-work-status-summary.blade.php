<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Summary
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
        }


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

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 5px;
        }

        table,
        tr {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        /* table tr {} */

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

        .td__center {
            text-align: center
        }


        .td__name {
            padding-bottom: 15px;
        }






        .td__total {
            font-weight: bold;
            text-align: center;
            color: black;
            font-size: 14px;
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
                <p> Employee Summary Month : <strong class="td__red__color"> {{ $month }}, {{$year}} </strong> </p>
                <p> <strong class="td__red__color"> </strong> </p>


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
        <section class="table__part">
            <table>
                <tbody>
                    <tr style="border-bottom:0;">
                        <th class="td__center td__name">S.N</th>
                        <td class="td__center td__emplyoeeId">Title</td>
                        <td class="td__center td__name">Total</td>
                    </tr>

                    <tr style="border-bottom:0;">
                        <th class="td__center td__name">1</th>
                        <td class="td__center td__emplyoeeId">Total Employees</td>
                        <td class="td__center td__name">{{$totalEmployee}}</td>
                    </tr>

                    <tr style="border-bottom:0;">
                        <th class="td__center td__name">2</th>
                        <td class="td__center td__emplyoeeId">Active Employees</td>
                        <td class="td__center td__name">{{$totalActiveEmployee}}</td>
                    </tr>

                    <tr style="border-bottom:0;">
                        <th class="td__center td__name">3</th>
                        <td class="td__center td__emplyoeeId">Others Employees</td>
                        <td class="td__center td__name">{{$totalEmployee - $totalActiveEmployee}}</td>
                    </tr>

                    <tr style="border-bottom:0;">
                        <th class="td__center td__name">3</th>
                        <td class="td__center td__emplyoeeId">Working Employees</td>
                        <td class="td__center td__name"> </td>
                    </tr>

                </tbody>
            </table>
        </section>
        <!-- ---------- -->


        </section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 50px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between">
                <p>Accountant</p>
                <p>Verified</p>
                <p>General Manager</p>
            </div>
        </div>
        {{-- Officer Signature --}}
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