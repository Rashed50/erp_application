<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Details
    </title>
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
            margin: 5px;

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
                max-width: 98%;
                margin: 0 auto 5px;

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
            
            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
        }


        .main__wrap {
            width: 90%;
            margin: 10px auto;
            margin-top: 5px;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: right;
            margin-bottom: 5px;
        }

        .title__part {
            align-items: center;
        }

        .print__part {}

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 5px;
        }

      

        /* Employee Information Table */

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table__part {
            display: flex;
        }


        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 10px;
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

        
        .td__amount {
            font-size: 14px;
            color: black;
            font-weight: 900;
            text-align: right;
            padding-right: 5px;
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
        <!-- Report Header part-->
        <section class="header__part">

            <div class="date__part">

            </div>
            <!-- title -->
            <div class="title__part">

                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address" style="text-align:center;">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>

        </section>

        <section>
            <br>
            <h2 style=" text-align:center; font-size:20px; "><b> INVOICE STATEMENT</b></h2><br>
        </section>
        <!-- Employee Info part -->
        <section class="table__part">
             <div style="width: 45%; bg-color:green">
                <table id="employeeinfo">
                    <tr>
                        <td>Project Name</td>
                        <td class="td__amount">{{$invoice_record_details->proj_name}}</td>
                    </tr>
                    
                    <tr>
                        <td>Total Employee</td>
                        <td class="td__amount">{{$invoice_project_info['total_employee']}}</td>
                    </tr>
                    <tr>
                        <td>Total Man Hours</td>
                        <td class="td__amount">{{$invoice_project_info['total_man_hours']}}</td>
                    </tr>
                    
                    <tr>
                        <td>Total Salary Amount</td>
                        <td class="td__amount">{{$invoice_project_info['total_salary']}}</td>
                    </tr>
                    <tr>
                        <td>Per Hour Rate</td>
                        <td class="td__amount">{{$invoice_project_info['hourly_cost']}}</td>
                    </tr>

                   
                    
                </table>
             </div>
             <div style="width:10%; bg-color:red"></div>
             <div style="width: 45%; bg-color:red">
                <table id="employeeinfo">
                    <tr>
                        <td>Contact No</td>
                        <td class="td__amount">{{$invoice_project_info['contract_no']}}</td>                
                    </tr>
                    <tr>
                        <td>Invoice Number</td>
                        <td class="td__amount">{{$invoice_record_details->invoice_no}}</td>                
                    </tr>
                    <tr>
                        <td>Invoice Date</td>
                        <td class="td__amount">{{$invoice_record_details->submitted_date}}</td>                
                    </tr> 

                    <tr>
                        <td>Total Amount of This Invoice [Exc. VAT]</td>
                        <td class="td__amount">{{$invoice_project_info['invoice_grand_total_amount']}}</td>                      
                    </tr>
                    <tr>
                        <td>Value Added Tax {{ $invoice_project_info['invoice_vat_percent']}}%</td>
                        <td class="td__amount">{{$invoice_project_info['invoice_total_vat']}}</td>                      
                    </tr>
                    <tr>
                        <td>Less Retention {{$invoice_project_info['invoice_retention_percent']}}%</td>
                        <td class="td__amount">{{$invoice_project_info['invoice_total_retention']}}</td>                      
                    </tr>
                    <tr>
                        <td>Total Amount [Exc. VAT & Retention]</td>
                        <td class="td__amount">{{$invoice_project_info['invoice_grand_total_amount'] - $invoice_project_info['invoice_total_retention']}}</td>                      
                    </tr>
                    <tr>
                        <td>Gross Total Amount [Inc. VAT]</td>
                        <td class="td__amount">{{$invoice_project_info['invoice_grand_total_amount'] + $invoice_project_info['invoice_total_vat']}}</td>                      
                    </tr>

                    <tr>
                        <td>Total Amount [Inc. VAT & Exc. retention]</td>
                        <td class="td__amount">{{ $invoice_project_info['invoice_grand_total_amount'] + $invoice_project_info['invoice_total_vat'] - $invoice_project_info['invoice_total_retention']  }}</td>                      
                    </tr>
                </table>
             </div>


        </section>
        
        <section>
            <br>
        </section>

         
        <section>
            <br>
        </section>
         
 
        
        <!-- Amount Summary part -->
        <section>
            <br>           
        </section>

        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
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
    
//        function showEmployeActivityRecord(){

//         var emp_id = document.getElementById('emp_id').innerHTML;               
//         var url = "{{ route('anemployee.activities.details.report', ':parameter') }}";
//         url = url.replace(':parameter', emp_id);
//         window.open(url, '_blank');

//    }
</script>

</html>