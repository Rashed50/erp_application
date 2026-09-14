<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Report
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
                max-width: 80%;
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


        
        .td__header {
            font-size: 10px;
            color: black;
            text-align: center;

        }
        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_name {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        .td__iqama {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__amount {
            font-size:10px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right:20px;
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

                <!-- <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address" style="text-align:center;">
                    {{$company->comp_address}}
                </address> -->
                
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>

        </section>

        <section>
            <br>
            <h3 style=" text-align:center; font-size:20px; "><b>Employee Payment Summary </b></h3><br>
        </section>
        <!-- Employee Info part -->
        <!-- <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td> Employee ID:</td>
                        <td class="td__bold"> {{ $employee->employee_id}}</td>
                        <td> Employee Name:</td>
                        <td class="td__bold">{{$employee->employee_name}}</td>                       
                    </tr>                    
                    <tr>
                        <td> Iqama No:</td>
                        <td> {{$employee->akama_no}}, {{$employee->akama_expire_date}}</td>
                        <td> Address:</td>
                        <td> {{$employee->mobile_no}}, {{$employee->details}},{{$employee->address}}</td>
                       
                       </tr>
                   
                </tbody>
            </table>
        </section> -->
        <section>
            <br>
            <!-- <p style=" text-align:center; font-size:16px; ">Head Office Payment Approved Details</p>   <br>
            -->
        </section>
        <!-- Head Office Payment Approved Details -->
        <section class="table__part">
            <table id="employeeinfo">        
                <thead>
                <tr>
                  <th class="td__header" >  S.N </th>
                  <th class="td__header"> Empl ID </th>
                  <th class="td__header"> Employee Name </th>
                  <th class="td__header"> Iqama No </th>
                  <th class="td__header"> Approved By </th>
                  <th class="td__header"> Date</th>
                  <th class="td__header"> Approved Amount</th> 
                             
                </tr>
                </thead>
                <tbody>
                @foreach ($approved_records as $emp)
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__iqama"> {{ $emp->employee->employee_id }} </td>
                  <td class="td__emplyoee_name"> {{ $emp->employee->employee_name }}</td>
                  <td class="td__iqama"> {{ $emp->employee->akama_no }} </td>
                  <td class="td__iqama"> {{ $emp->approvedBy->employee_name ?? " " }} </td>
                  <td class="td__iqama"> {{ $emp->approved_date }} </td>                
                  <td class="td__amount"> <a href=" {{   route('employee.payment.from-bd-office.emp.payment-details', ['id' => $emp->employee->employee_id ]) }}" target="_blank" >
                      {{ $emp->approved_amount }} </a> </td>
                 
                 </tr>
                 
                @endforeach             
                  
            </table>
        </section>

        <!-- BD Office Payment Details -->
        <section>
            <br>
            <p style=" text-align:center; font-size:16px; ">BD Office Payment Details</p>
        </section>
        <section>
            <table id="employeeinfo">
                
            </table>
        </section>

        <section>
            <br>
            <table id="employeeinfo">
                <tbody>
                <thead>
                <tr>
                  <th class="td__header" >  S.N </th>
                  <th class="td__header"> Receiver Name </th>
                  <th class="td__header"> Mobile No </th>
                  <th class="td__header"> Address </th>
                  <th class="td__header"> Relation with Emp.  </th>
                  <th class="td__header"> Payment Method</th>
                  <th class="td__header"> Date</th> 
                  <th class="td__header" > Amount (SAR) </th> 
                  <th class="td__header" >Exchange Rate </th> 
                  <th class="td__header" > Amount (BDT) </th> 
                </thead>
                <tbody>
                    @php
                        
                        $total_paid_sar_amount = 0;
                        $total_paid_bdt_amount = 0;
                    @endphp
                @foreach ($payment_records as $emp)
                    @php
                        
                        $total_paid_sar_amount += $emp->sar_paid_amount;
                        $total_paid_bdt_amount += $emp->bdt_paid_amount;
                    @endphp
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>                  
                  <td class="td__iqama"> {{ $emp->receiver_name }} </td>
                  <td class="td__emplyoee_name"> {{ $emp->receiver_mobile }}</td>
                  <td class="td__iqama"> {{ $emp->receiver_address }} </td>
                  <td class="td__iqama"> {{ $emp->relation_with_emp_id}} </td>
                  <td class="td__iqama">  {{ $emp->payment_method }} </td>
                  <td class="td__iqama"> {{ $emp->updated_at }} </td>
                  <td class="td__amount"> {{ $emp->sar_paid_amount }} </td>
                  <td class="td__amount"> {{ $emp->exchange_rate }} </td>
                  <td class="td__amount"> {{ $emp->bdt_paid_amount }} </td>
                 </tr>
                 
                @endforeach
              </tbody>
              <tr>
                    <td colspan="7" class="td__amount"> Total</td>
                    <td class="td__amount"> {{ $total_paid_sar_amount }} </td>
                    <td class="td__amount">  </td>
                    <td class="td__amount"> {{ $total_paid_bdt_amount }} </td>
                 

              </tr>
               

                </tbody>
            </table>
            </table>

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
</script>

</html>