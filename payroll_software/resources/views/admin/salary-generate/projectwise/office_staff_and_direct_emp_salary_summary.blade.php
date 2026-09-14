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
                margin: 5mm 10mm 10mm 10mm;
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
                <p>   <strong class="td__red__color"> {{ $report_title }} </strong> </p>


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
        <h3 style="text-align:center; color:red">Office Staff and Direct Employees Salary Summary </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>

                    <colgroup>
                        <col span="2" style="background-color: lightgray"  >
                        <col span="3" style="background-color:#D7BDE2" >
                        <col span="3" style="background-color:#AED6F1">
                        <col span="3" style="background-color:#ABEBC6">
                        <col span="3" style="background-color:lightgray" >
                    </colgroup>
                    <tr>
                        <th colspan="2" class="th_header_section"> </th>
                        <th colspan="3" class="th_header_section">Basic Employees</th>                    
                        <th colspan="3" class="th_header_section">Hourly Employees</th>
                        <th colspan="3" class="th_header_section">Office Staff</th>     
                        <th colspan="3" class="th_header_section">Total</th>                  
                    </tr>                  

                    <tr>
                        <th>S.N</th>
                        <th>Project</th>
                        
                        <th>Emp. <br></th>
                        <th>Hours<br></th>
                        <th>Salary</th>

                        <th>Emp. <br></th>
                        <th>Hours<br></th>
                        <th>Salary</th>
 
                        <th>Emp. <br></th>
                        <th>Hours<br></th>
                        <th>Salary</th>

                        <th>Total <br> Emp.</th>
                        <th>Total <br>Hours</th>
                        <th>Total <br>Salary</th>                                      
                    </tr> 

                    @php 
                        
                        $grand_total_emp = 0 ;
                        $grand_total_hours = 0;
                        $grand_total_salary = 0; 

                        $grand_total_basic_emp = 0 ;
                        $grand_total_basic_hours = 0;
                        $grand_total_basic_salary = 0;


                        $grand_total_hourly_emp = 0 ;
                        $grand_total_hourly_hours = 0;
                        $grand_total_hourly_salary = 0;


                        $grand_total_indirect_emp = 0 ;
                        $grand_total_indirect_hours = 0;
                        $grand_total_indirect_salary = 0;
                        
                        
                    @endphp 

                    @foreach($project_list as $ar)  
                    @php 
                       
                        $ap_total_emp = $ar->total_basic_emp + $ar->total_hourly_emp + $ar->total_indirect_emp ;
                        $ap_total_hours =  $ar->total_basic_hours + $ar->total_hourly_hours + $ar->total_indirect_hours ;
                        $ap_total_salary = $ar->total_basic_salary + $ar->total_hourly_salary + $ar->total_indirect_salary ;
                        

                        
                        $grand_total_basic_emp += $ar->total_basic_emp ;
                        $grand_total_basic_hours += $ar->total_basic_hours  ;
                        $grand_total_basic_salary += $ar->total_basic_salary  ;


                        $grand_total_hourly_emp += $ar->total_hourly_emp ;
                        $grand_total_hourly_hours += $ar->total_hourly_hours  ;
                        $grand_total_hourly_salary += $ar->total_hourly_salary  ;

                        $grand_total_indirect_emp += $ar->total_indirect_emp ;
                        $grand_total_indirect_hours += $ar->total_indirect_hours  ;
                        $grand_total_indirect_salary += $ar->total_indirect_salary  ;



                        $grand_total_emp +=  $ap_total_emp;
                        $grand_total_hours +=  $ap_total_hours;
                        $grand_total_salary +=  $ap_total_salary;

                    @endphp  
                      

                    <tr>
                        <td class="td__sn">{{$loop->iteration}}</td>
                        <td class="td__info">{{$ar->proj_name }}</td>

                        <td class="td__sn">{{ $ar->total_basic_emp }} </td> 
                        <td class="td_amount"> {{ number_format(round(($ar->total_basic_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_amount"> {{ number_format(round(($ar->total_basic_salary),2) , 2, '.', ',')}} </td>
                      

                        <td class="td__sn">{{ $ar->total_hourly_emp }} </td> 
                        <td class="td_amount"> {{ number_format(round(($ar->total_hourly_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_amount"> {{ number_format(round(($ar->total_hourly_salary),2) , 2, '.', ',')}} </td>
                      
                        <td class="td__sn">{{ $ar->total_indirect_emp }} </td> 
                        <td class="td_amount"> {{ number_format(round(($ar->total_indirect_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_amount"> {{ number_format(round(($ar->total_indirect_salary),2) , 2, '.', ',')}} </td>

                        <td class="td__sn">{{ $ap_total_emp }} </td> 
                        <td class="td_total_amount"> {{ number_format(round(($ap_total_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount"> {{ number_format(round(($ap_total_salary),2) , 2, '.', ',')}} </td>
                       
   
                    </tr>

                    @endforeach
 
                    <tr>
                  
                        <td colspan="2" class="td_amount"> <b>Total </b></td>

                        <td class="td__sn">{{ $grand_total_basic_emp }} </td> 
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_basic_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_basic_salary),2) , 2, '.', ',')}} </td>
                    
                        <td class="td__sn">{{ $grand_total_hourly_emp }} </td> 
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_hourly_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_hourly_salary),2) , 2, '.', ',')}} </td>
                    
                        <td class="td__sn">{{ $grand_total_indirect_emp }} </td> 
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_indirect_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_indirect_salary),2) , 2, '.', ',')}} </td>
                       
                        <td class="td__sn">{{ $grand_total_emp }} </td> 
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_hours),2) , 2, '.', ',')}} </td>
                        <td class="td_total_amount"> {{ number_format(round(($grand_total_salary),2) , 2, '.', ',')}} </td>
                    
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