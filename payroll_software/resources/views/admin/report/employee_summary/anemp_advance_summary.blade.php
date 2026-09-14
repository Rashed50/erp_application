<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$employee == null ? "": $employee->employee_id }}-Salary Records
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
            /* border: 1px solid black; */
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
                size: A4 portrait;
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
            align-items: center;
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

        table,
        tr {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        table th {
            font-size: 12px;
        }

        table td {
            text-align: left;
            font-size: 12px;

        }

        th,
        td {
            padding: 5px 2px;
            /* Top,Right,Bottom,left */
        }

        .td__center {
            text-align: center
        }

        .td__bold {
            font-weight: 700;
        }

        .td__emplyoeeId {
            font-size: 13px;
            padding-bottom: 15px;
            color: blue;
            font-weight: 900;
            text-align: center
        }

         .td__month_year {
            font-size: 13px;
            color: navy;
            font-weight: 900;
            text-align: left
        }
        .td__sponser {
            color: green;
            font-weight: 300;
            text-align: center;
        }

        .sponser__name {
            font-size: 10px;
            font-weight: 100;
        }

        .country {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .employe__trade {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .td__project {
            font-size: 12px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }

        .td__gross_salary {
            font-size: 13px;
            color: black;
            font-weight: 900;
            text-align: right;
            padding-right: 5px;
        }




        .table__part {
            display: flex;
        }


        #emp_details_table{
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #emp_details_table, th, td {
            border: 1px dotted;
            padding-left: 5px;
        }

        .emp_td_header{
            text-align: left;
            font-style: bold;
            color: black;
            padding-left:5px;
            font-weight: bold

        }
        .t__total_row{
            font-weight: bold;
            font-size:10px;
            background-color: lightgray;
            color: black;
            text-align: center;


        }
        .td__amount{
            text-align: right;
            padding-right:5px;
            color: black;

        }

        .sm_td_header{
            text-align: left;
            color: black;
            padding-left:5px;
            font-weight: bold

        }

        /* Employee Information Table */

        #salary_table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #salary_table td,
        #salary_table th {
            font-size: 10px;
            padding: 5px;
            border-top: 1px solid black;    /* Keep horizontal lines */
             border-bottom: 1px solid black; /* Keep horizontal lines */
            border-left: none;              /* Remove vertical lines */
            border-right: none;             /* Remove vertical lines */
            border-collapse: collapse;
        }



        #salary_table tr:hover {
            background-color: #ddd;
        }

        #salary_table th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
            font-weight: bold;
            height: 30px;

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
                <p> Print: <br> {{ Carbon\Carbon::parse( Carbon\Carbon::now())->format('d-m-Y h:i A')}} </p>
            </div>
            <!-- title -->
            <div class="title__part">
                <br>
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address" style="text-align:center;">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
               <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>

        </section>
        <br>
        <!-- Employee Info part -->
        <section class="table__part">
             <table id="emp_details_table">
                <thead>
                    <tr>
                        <th colspan="4" style="text-align: center;background:rgb(138, 219, 118, 0.46);padding-top:5px;padding-bottom:5px;font-size: 13px;">Employee Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="emp_td_header"> Employee ID</td>
                              <td class="emp_td_header"> <span id ="emp_id">{{$employee->employee_id}}</span> </td>
                        <td class="emp_td_header"> Employee Status</td>
                         <td class="emp_td_header"><b>
                            @if($employee->job_status == 1)
                                Active
                            @elseif($employee->job_status == 2)
                                Inactive
                            @elseif($employee->job_status == 3)
                                Final Exit
                            @elseif($employee->job_status == 4)
                                Release
                            @elseif($employee->job_status == 5)
                                 Vacation
                            @elseif($employee->job_status == 6)
                                 Runaway
                            @endif
                            , {{ $employee->salary_status == 1 ? 'Salary: Active' : 'Salary: Hold' }}
                         </b>
                        </td>
                    </tr>
                    <tr>
                        <td class="emp_td_header"> Employee Name</td>
                        <td class="emp_td_header">{{$employee->employee_name}}</td>
                        <td class="emp_td_header"> Sponsor Name</td>
                        <td style="text-transform: capitalize;"> {{ ucwords(str_replace(['-', '_'], ' ', $employee->sponser->spons_name)) }} </td>
                    </tr>
                    <tr>
                        <td class="emp_td_header"> Iqama No</td>
                        <td> {{$employee->akama_no}}, {{  \Carbon\Carbon::parse($employee->akama_expire_date)->format('d-m-Y') }}</td>
                        <td class="emp_td_header"> Designation</td>
                        <td> {{$employee->employeeType->name}}, {{$employee->category->catg_name}} </td>
                    </tr>
                    <tr>
                        <td class="emp_td_header"> Passport No</td>
                        <td> {{$employee->passfort_no}}, {{  \Carbon\Carbon::parse($employee->passfort_expire_date)->format('d-m-Y') }}</td>
                         <td class="emp_td_header"> Mobile No</td>
                        <td> {{$employee->mobile_no }}, {{$employee->phone_no }}</td>
                    </tr>
                     <tr>
                        <td class="emp_td_header"> Agency Name</td>
                        <td> {{$employee->agency->agc_title}}</td>
                        <td class="emp_td_header"> Joining Date</td>
                        <td> {{  \Carbon\Carbon::parse($employee->joining_date)->format('d-m-Y')   }}</td>
                    </tr>
                    <tr>
                        <td class="emp_td_header">National Address</td>
                        <td>
                             {{$employee->details == null ? "":$employee->details.", "}}{{$employee->address == null ? "":$employee->address.", "}} {{$employee->country->country_name}}
                        </td>
                        <td class="emp_td_header">Working Project</td>
                        <td style="text-transform: capitalize;"> {{ ucwords(str_replace(['-', '_'], ' ', $employee->project->proj_name)) }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </section>
        <br>
        <!-- Advance Details Records -->
        <section class="table__part">
             <table id="employeeinfo">
                <thead>
                    <tr> <td colspan="12" style="text-align: center;background:rgba(118, 153, 219, 0.46);padding-top:5px;padding-bottom:5px;font-size: 13px;font-weight: bold;"> ADVANCE AND DEDUCTION DETAILS OF {{ $employee->employee_id}}, {{$employee->employee_name}} </td></tr>
                    <tr>
                        <th>SL No </th>
                        <th> Month, Year </th>
                        <th> Iqama Expense </th>
                        <th>Renew Date</th>
                        <th>Iqama Paid</th>
                        <th>Advance Received</th>
                        <th>Advance Date </th>
                        <th>Advance Paid</th>
                        <th> Remarks </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_iqama_expense = 0;
                        $total_iqama_paid = 0;
                        $total_other_advance = 0;
                        $total_advance_paid = 0;

                    @endphp

                    @foreach($results  as $arecord)
                       @php
                            $total_iqama_expense += $arecord->iqama_expense;
                            $total_iqama_paid += $arecord->iqama_paid;
                            $total_other_advance += $arecord->other_advance;
                            $total_advance_paid += $arecord->advance_paid;

                        @endphp
                    <tr>
                        <td class="td__center">{{ $loop->iteration }}</td>
                        <td class="td__center"> {{ date('F', mktime(0, 0, 0, $arecord->month, 10)); }} ,{{ $arecord->year  }}    </td>
                        <td class="td__amount"> {{ $arecord->iqama_expense == 0 ? '-' : $arecord->iqama_expense  }} </td>
                        <td class="td__amount"> {{ $arecord->iqama_expense_date == null ? "-" : $arecord->iqama_expense_date }}</td>
                        <td class="td__amount"> {{ $arecord->iqama_paid  == 0 ? '-' : $arecord->iqama_paid  }}</td>
                        <td class="td__amount"> {{ $arecord->other_advance   == 0 ? '-' : $arecord->other_advance }} </td>
                        <td class="td__amount"> {{ $arecord->other_advance_date == null ? "-" : $arecord->other_advance_date  }}</td>
                        <td class="td__amount"> {{ $arecord->advance_paid   == 0 ? '-' : $arecord->advance_paid  }} </td>
                        <td class="td__center"> {{ $arecord->other_remarks == null ? "-" : $arecord->other_remarks  }} </td>
                    </tr>
                    @endforeach

                    <tr  class="t__total_row">
                        <td class="td__center" colspan="2">Total </td>
                        <td class="td__gross_salary">{{ $total_iqama_expense }} </td>
                        <td class="td__center">-</td>
                        <td class="td__gross_salary">{{ $total_iqama_paid }} </td>
                        <td class="td__gross_salary">{{ $total_other_advance }} </td>
                        <td class="td__center"> - </td>
                        <td class="td__gross_salary">{{ $total_advance_paid }} </td>
                        <td class="td__center">- </td>
                    </tr>


                </tbody>
            </table>

        </section>



        <section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; text-align: center; ">
                    <p>  <b>  ------------------------ <br> {{$login_name}} </b> <br> Prepared By  </p>
                    <p>  <b>  ------------------------ <br>   </b> Accounts/HR  </p>
                    <p>  <b>  ------------------------ <br>   </b> Approved By  </p>
                </div>
            </div>
            <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p>
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
