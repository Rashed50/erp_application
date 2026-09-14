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
                max-width: 90%;
                margin: 5px;

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

        .address {
            font-size: 10px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
        }

        /* table part */
        .td__center {
            text-align: center
        }

        .td__bold {
            font-weight: bold;
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
            padding: 0px;
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
            <h5 style=" text-align:center; font-size:12px; padding-bottom:5px; "><b> Employee Information Details</b></h5>
        </section>

          <!-- Employee Info header part start -->
          <section class="table__part">
           

            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td class="td__bold">Employee ID:</td>
                        <td class="td__bold"> {{ $employee->employee_id}}</td>
                        <td rowspan="5" style="width:100px;height:100px;">
                            @if ($employee->profile_photo != null)
                                <img src="{{ asset($employee->profile_photo) }}" alt="profile_img" class="image-resize" height="100" width="100">
                            @else
                                <img src="{{ asset('contents/admin') }}/assets/images/avatar.png"  alt="photo not found" height="100" width="100">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="td__bold"> Employee Name:</td>
                        <td class="td__bold">{{$employee->employee_name}}</td>
                    </tr>

                    <tr>
                        <td class="td__bold"> Address:</td>
                        <td class="td__bold"> {{$employee->details}},{{$employee->district_name}},{{$employee->division_name}},{{$employee->country_name}}</td>
                    </tr>

                    <tr>
                        <td class="td__bold"> Project:</td>
                        <td class="td__bold"> {{$employee->project->proj_name}} </td>
                    </tr>
                    <tr> 
                        <td class="td__bold">  Salary Status:</td>
                        <td class="td__bold">  {{$employee->salary_status == 1 ? 'Active' : 'Salary Holding'  }} </b> </td>
                    </tr>
                   
                </tbody>
            </table> 
        </section>

        <!-- Employee Info part -->
        <section class="table__part">           

            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td class="td__bold"> Iqama No:</td>
                        <td class="td__bold"> {{$employee->akama_no}} </td>
                        <td class="td__bold"> Iqama Expire Date:</td>
                        <td class="td__bold"> {{$employee->akama_expire_date}}</td>
                    </tr>
                    <tr>
                        <td> Passport No:</td>
                        <td> {{$employee->passfort_no}} </td>
                        <td> Passport Expire Date:</td>
                        <td> {{$employee->passfort_expire_date}}</td>
                    </tr>
                    <tr>
                        <td> Agency Name:</td>
                        <td> {{ $employee->agency->title }}
                        </td>
                        <td> Sponsor Name:</td>
                        <td>
                            @if ($employee->sponsor_id == null)
                            No Assigned Sponsor!
                            @else
                            {{ $employee->spons_name }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> Employee Type:</td>
                        <td>
                            @if ($employee->employee_name == 1)
                            Direct Manpower
                            @else
                            Indirect Manpower
                            @endif
                        </td>
                        <td> Designation:</td>
                        <td>  {{ $employee->catg_name }} </td>
                    </tr>
                    <tr>
                        <td> Working Project:</td>
                        <td> {{$employee->project->proj_name}} </td>
                        <td> Working Shift:</td>
                        <td>
                            @if ($employee->isNightShift == 0)
                            Day Shift
                            @else
                            Night Shift
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td> Joining Date:</td>
                        <td> {{ Carbon\Carbon::parse($employee->joining_date)->format('D, d F Y') }} </td>
                        <td> Email:</td>
                        <td> {{ $employee->email}} </td>
                    </tr>
                    <tr>
                        <td> Present Address:</td>
                        <td> {{$employee->present_address }}</td>
                        <td> Mobile:</td>
                        <td> {{$employee->mobile_no }}, {{$employee->phone_no }} </td>
                    </tr>
                    <tr> 
                        <td>  Joining Date:</td>
                        <td class="td__bold">  {{$employee->joining_date }}   </td>
                        <td>  Villa Name:</td>
                        <td >  {{$employee->ofb_name }}   </td>
                    </tr>
                    <tr> 
                        <td class="td__bold"> Employee Working Status:</td>
                        <td class="td__bold">{{$employee->title}}  </td>
                        <td></td>
                        <td></td>
                        
                    </tr>
                </tbody>
            </table>

             
        </section>

        <section>
            <br>
            <p style=" text-align:center; font-size:16px; ">Employee Salary Details</p>
        </section>

        <!-- Salary Details part -->
        <section class="table__part">

            <table id="employeeinfo">
                <tbody>                   
                    <tr>
                        <td> Basic Amount:</td>
                        <td> {{$employee->basic_amount}} </td>
                        <td> House Rent:</td>
                        <td> {{$employee->house_rent}}</td>
                        <td> Mobile Allowance:</td>
                        <td> {{$employee->mobile_allowance}} </td>
                    </tr>
                    <tr>
                        <td> Hourly Rate:</td>
                        <td> {{$employee->hourly_rent }}</td>
                        <td> Food Allowance:</td>
                        <td> {{$employee->food_allowance}} </td>
                        <td> Medical Allowance:</td>
                        <td> {{$employee->medical_allowance }}</td>
                    </tr>
                    <tr>
                        <td> Local Travels Allowance:</td>
                        <td> {{$employee->local_travel_allowance }} </td>
                        <td> Conveyance Allowance:</td>
                        <td> {{$employee->conveyance_allowance }}</td>
                        <td> Increment No:</td>
                        <td> {{$employee->increment_no }} </td>
                    </tr>
                    <tr>
                        <td> Increment Amount:</td>
                        <td> {{$employee->increment_amount  }}</td>
                        <td> Others:</td>
                        <td> {{$employee->others1  }} </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="blank_space"></div>
        </section>
        <!-- Employee Salary Details part end -->

        

        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Prepared By<br> {{ $loggedInUser }}</p>
                    
                    <p>Verified</p>
                    <p>General Manager</p>
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
