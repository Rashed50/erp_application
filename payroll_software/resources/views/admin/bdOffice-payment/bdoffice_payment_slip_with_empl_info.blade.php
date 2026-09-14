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
                margin:5px;

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
            width: 95%;
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
            font-size:14px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
       

       

        .td__center {
            text-align: center
        }

        .td__bold {
            font-weight: 400;
        }
         .td__project {
            font-size: 8px;
 
            color: red;
            font-weight: 100;
            text-align: center;

        }

        .td__gross_salary {
            font-size: 11px;
            color: navy;
            font-weight: 200;
            text-align: center
        }

        .box__signature {
            width: 150px;
            height: 20px;
            border: 1px solid gray;
            margin: 0px;
            padding:0px;
            color: lightgray;
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
           font-size:10px;
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
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
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

        <section>
            <h5 style=" text-align:center; font-size:12px; padding-bottom:5px; "><b> Employee Payment Slip</b></h5> 
        </section>
        <!-- Employee Info part -->
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td> Employee ID:</td>
                        <td class="td__bold"> {{ $employee->employee_id}}</td>
                        <td> Emp. Status:</td>
                        <td class="td__bold"> {{$employee->job_status == 1 ? "Active": "InActive" }}</td>
                    </tr>
                    <tr>
                        <td> Employee Name:</td>
                        <td class="td__bold">{{$employee->employee_name}}</td>
                        <td> Address:</td>
                        <td>{{$employee->details}},{{$employee->address}},{{$employee->country->country_name}} </td>
                    </tr>

                    <tr>
                        <td> Passport No:</td>
                        <td> {{$employee->passfort_no}}, {{$employee->passfort_expire_date}}</td>
                        <td> Mobile:</td>
                        <td> {{$employee->mobile_no}}</td>
                    </tr>

                    <tr>
                        <td> Iqama No:</td>
                        <td> {{$employee->akama_no}}, {{$employee->akama_expire_date}}</td>
                        <td> Trade:</td>
                        <td>{{$employee->category->catg_name}}  </td>
                    </tr>
                    <tr>
                        <td> Agency Name:</td>
                        <td> {{$employee->agency->title}} </td>
                        <td> Joining:</td>
                        <td> {{$employee->joining_date }} </td>
                    </tr>
                    
                     <!-- Payment Information-->
                     <tr> <td colspan="4" style="text-align: center;"> <br><br><br> <b>Employee Payment Details Information </b></td> </tr>
                   
                   
                     <tr>
                        <td> Amount Receiver By:</td>
                        <td class="td__bold"> {{ $employee->receiver_name}}</td>

                        <td> Address:</td>
                        <td class="td__bold"> {{$employee->receiver_address }}</td>
                    </tr>
                    <tr>
                        <td> Mobile No:</td>
                        <td class="td__bold">{{$employee->receiver_mobile}}</td>
                        <td> Relation with Employee:</td>
                        <td class="td__bold">{{$employee->relation_with_emp}}  </td>
                    </tr>
                    <tr>
                        <td> Payment Received Date:</td>
                        <td>   {{$employee->payment_received_date}}</td>
                        <td>Payment Method:</td>
                        <td>  {{$employee->payment_method}}  {{$employee->transaction_details}}  </td>
                    </tr> 

                    <tr>
                    <tr>
                        <td colspan="2" style="text-align: center;"><b> Approved Amount : {{$employee->approved_amount}}</b> </td>  
                        <td colspan="2" style="text-align: center;"><b> Pay Amount : {{$employee->paid_amount}}</b> </td>  
                    </tr> 


                </tbody>
            </table>
        </section>
        <!-- <br><br>
        <h5 style="tet-align:center"> Employee Payment Details Information <h5>
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td> Amount Receiver By:</td>
                        <td class="td__bold"> {{ $employee->receiver_name}}</td>

                        <td> Address:</td>
                        <td class="td__bold"> {{$employee->receiver_address }}</td>
                    </tr>
                    <tr>
                        <td> Mobile No:</td>
                        <td class="td__bold">{{$employee->receiver_mobile}}</td>
                        <td> Relation with Employee:</td>
                        <td class="td__bold">{{$employee->relation_with_emp}}  </td>
                    </tr>
                    <tr>
                        <td> Payment Received Date:</td>
                        <td>   {{$employee->payment_received_date}}</td>
                        <td>Payment Method:</td>
                        <td>  {{$employee->payment_method}}  {{$employee->transaction_details}}  </td>
                    </tr> 
                </tbody>
            </table>
        </section> -->
        <br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Reciver Signature</p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
        <br><br>
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