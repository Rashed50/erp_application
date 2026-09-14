<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Copy Report</title>
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
            border: 1px solid black;
            margin: 15px;

        }

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
                margin: 5px;

            }


            @page {
                size: A4 landscape;
                margin: 5mm 0mm 5mm 0mm;
                /* top, right,bottom last value was= 10, left */

            }

            a[href]:after {
                content: none !important;
            }

            .print__button {
                visibility: hidden;
            }
        }


        .main__wrap {
            width: 90%;
            margin: 5px auto;

        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .title__part {
            text-align: center;
            font-size: 14px;
        }

        .title_left__part {
            text-align: left;
            font-size: 10px;
        }


        .print__part {
            text-align: right;
            font-size: 10px;
        }

        .td__center {
            text-align: center
        }

        .td__bold {
            font-weight: 400;
        }

        .td__gross_salary {
            font-size: 15px;
            color: black;
            font-weight: 600;
            text-align: left
        }

        .td__title{
            font-weight:bold;
            text-align:center;
            font-size:14px;
        }

        .box__signature {
            
            height: 50px;
            border: 1px solid gray;
            margin: 0px;
            padding-bottom: 20px;
            text-align: center;s
            color: black;
        }



        /* Employee Information Table */

        #employeeinfo {
            font-family: "Times New Roman", Times, sans-serif;
            border-collapse: collapse;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
        }

        .table__part {
            display: flex;
        }


        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 13px;
            padding: 5px;
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
            text-align: left;
            background-color: #EAEDED;
            color: black;
        }

         
    </style>
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

            <div class="title_left__part">
            </div>
            <!-- Company Information title -->
            <div class="title__part">
                
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now() }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>

        <section>
            <h5 style=" text-align:center; font-size:12px; padding-bottom:5px; "><b> Summary Report of Receiver Copy</b></h5>
        </section>
        <br><br>
        <!-- Employee Info part -->
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td colspan="2"> Reciver ID</td>
                        <td class="td__bold" colspan="2"> <span id ="emp_auto_id" hidden>{{$employee->emp_auto_id}}</span> {{ $employee->employee_id}}</td>
                     </tr>
                    <tr>
                        <td colspan="2">  Name of Receiver</td>
                        <td class="td__bold" colspan="2">{{$employee->employee_name}}</td>                    
                    </tr>              
                    
                    <tr>
                        <td colspan="2"> Address:</td>
                        <td colspan="2"> {{$employee->mobile_no}},
                            {{$employee->details}},{{$employee->address}},{{$employee->country->country_name}}
                        </td>                           
                    </tr>

                    <tr>
                        <td class="td__title" colspan="2"> 
                               {{  $closed_fiscal_record->end_date != null ? $closed_fiscal_record->end_date : 'PREVIOUS' }} CLOSING BALANCE
                            </td>
                        <td class="td__title" colspan="2"> 
                               {{ $closed_fiscal_record->balance_amount   }}  
                            </td>                        
                     </tr>

                    <tr>
                        <td class="td__title" colspan="2">Received Details</td>
                        <td class="td__title" colspan="2">Paid Details</td>
                     </tr>

                     <tr>
                        <td class="td__title"> Total Unpaid Salary Amount </td>
                        <td class="td__gross_salary">  {{$totalUnPaidSalaryAmount }} </td>
                        <td class="td__title"> Iqama Deduction Amount </td>
                        <td class="td__gross_salary">  {{$toal_iqama_expense_deduction_from_salary }} </td>
                         
                    </tr>

                    <tr>
                        <td class="td__title">Iqama Renewal Expence </td>
                        <td class="td__gross_salary">{{ $iqamaRenewalTotalExpence }} </td> 
                        <td class="td__title"> Cach Paid  </td>
                        <td class="td__gross_salary">  {{$cashReceiveTotalPaidAmount }} </td>
                    </tr>

                    <tr>
                        <td class="td__title">Other Advance Amount </td>
                        <td class="td__gross_salary">{{ $otherAdvanceTotalAmount }} </td> 
                        <td class="td__title"> Other Advance Deduction Amount </td>
                        <td class="td__gross_salary">  {{$total_other_advace_deduction_from_salary }} </td>
                    </tr>

                    <tr>
                        <td class="td__title">Total Payable   </td>
                        <td class="td__gross_salary">{{ $totalUnPaidSalaryAmount + $iqamaRenewalTotalExpence + $otherAdvanceTotalAmount }} </td> 
                        <td class="td__title">Total Receivable   </td>
                        <td class="td__gross_salary">{{ $toal_iqama_expense_deduction_from_salary +
                              $total_other_advace_deduction_from_salary+$cashReceiveTotalPaidAmount +
                               $totalUnPaidSalaryAmount }} </td> 
                    </tr>

                    <tr>
                        <td class="box__signature" colspan="4"> Closing Balance 
                             {{  ($totalUnPaidSalaryAmount + $iqamaRenewalTotalExpence + $otherAdvanceTotalAmount + $closed_fiscal_record->balance_amount) - ($toal_iqama_expense_deduction_from_salary +
                              $total_other_advace_deduction_from_salary+$cashReceiveTotalPaidAmount +
                               $totalUnPaidSalaryAmount ) }} </td>
                        
                    </tr>


                    <tr>
                        <td class="box__signature" colspan="4">  Signature of {{$employee->employee_name}} </td>
                        
                    </tr> 

                </tbody>
            </table>
        </section>

        <br><br><br><br><br>
         <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_user}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>

    </div>
 

</body>

<script>
    $(window).on('load', function () { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>



</html>
