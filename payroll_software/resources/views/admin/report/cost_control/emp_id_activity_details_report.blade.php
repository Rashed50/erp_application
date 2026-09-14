<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report </title>
 
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
            font-size:12px;
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
            <!-- Left Part -->
            <div class="date__part">
                <p>   <strong class="td__red__color"> </strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- Right Part -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>

                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <h3 style="text-align:center; color:red"> {{ $report_title }}   </h4> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <th>S.N</th>
                        <th>Project</th>
                        <th>Plot</th>
                        <th>Employee</th>
                        <th>Emp. ID</th>
                        <th>Activity Element</th>                    
                        <th>Work</th>
                        <th>Working Shift</th>
                        <th>Date</th>
                        <th>Remarks</th>
                        <th>Working Hours</th>
                        <th>Total Worker</th>
                        <th>Total Hours</th> 
                                        
                    </tr> 

                    @php 
                        $total_workers = 0;
                        $total_hours = 0;                                       
                    @endphp 

                    @foreach ($records as $arecord  )  
                    @php 
                                       
                            $total_workers +=  $arecord->total_worker;
                            $total_hours +=  $arecord->total_working_hours; 
                    @endphp                      
                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{  $arecord->employee_name }}</td>
                        <td class="td__info">{{  $arecord->employee_id }}</td>
                        <td class="td__info">{{  $arecord->proj_name }}</td>
                        <td class="td__info">{{  $arecord->plo_name }}</td>
                        <td class="td__info">{{  $arecord->act_ele_name }}</td>
                        <td class="td__info">{{  $arecord->act_name }}</td>
                        <td class="td__info">{{  $arecord->working_shift }}</td>
                        <td class="td__info">{{  $arecord->working_date }}</td>
                        <td class="td__info">{{  $arecord->remarks }}</td>
                        <td class="td_amount">{{  $arecord->daily_hours }}</td>
                        <td class="td_amount">{{  $arecord->total_worker }}</td>
                        <td class="td_total_amount">{{number_format(round($arecord->total_working_hours,2) , 2, '.', ',')}}</td>                  
                    </tr>
                    @endforeach
                    <tr>                        
                        <td colspan="11" class="td_total_amount">Total =</td>
                        <td class="td_total_amount">{{number_format( round( $total_workers ,2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount">{{number_format( round( $total_hours ,2) , 2, '.', ',')}} </td>
                     </tr>
   
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 50px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Accountant</p>
                    <p>Verified</p>
                    <p>General Manager</p>
                </div>
            </div>
            {{-- Officer Signature --}}
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