<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Vehicle Details
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
    <!-- loading animation library -->
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
            <h5 style=" text-align:center; font-size:12px; padding-bottom:5px; "><b> Vehicle Information Details</b></h5>
        </section>

          <!-- Employee Info header part start -->
          <section class="table__part">
           

            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td class="td__bold"> </td>
                        <td class="td__bold"> </td>
                        <td rowspan="5" style="width: 120px;">
                            @if ($vehicle->veh_photo != null)
                                <img src="{{ asset($vehicle->veh_photo) }}" alt="profile_img" class="image-resize">
                            @else
                                <img src="{{ asset('contents/admin') }}/assets/images/avatar.png" alt="profile_img" class="image-resize" alt="Profile Picture" height="120px;" width="120px;">
                            @endif
                        </td>
                    </tr>                 
                   
                </tbody>
            </table> 
        </section>

        <!-- Employee Info part -->
        <section class="table__part">           
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td class="td__bold">Name:</td>
                        <td class="td__bold"> {{$vehicle->veh_name}} </td>
                        <td class="td__bold"> Plate No: </td>
                        <td class="td__bold"> {{$vehicle->veh_plate_number}}</td>
                        <td class="td__bold">Brand/Model:</td>
                        <td class="td__bold"> {{$vehicle->veh_model_number}}, {{$vehicle->veh_brand_name}}</td>
                    </tr>   
                    <tr>
                        <td class="td__bold">Insur. Date:</td>
                        <td class="td__bold"> {{$vehicle->veh_insurrance_date}} {{$vehicle->veh_ins_expire_date}} </td>
                        <td class="td__bold"> Purchase/Regs. Renewal </td>
                        <td class="td__bold"> {{$vehicle->veh_purchase_date}} {{ $vehicle->veh_reg_renew_date}} </td>
                        <td class="td__bold"> </td>
                        <td class="td__bold">  </td>
                    </tr>  
                    <tr>
                        <td class="td__bold">Insurrance File: </td>
                        <td class="td__bold"><a target="_blank" href="{{URL::to($vehicle->veh_ins_certificate) }}"  class="btn btn-danger">Download</a>  </td>
                        <td class="td__bold"> Registration File </td>
                        <td class="td__bold"><a target="_blank" href="{{URL::to($vehicle->veh_reg_certificate) }}"  class="btn btn-danger">Download</a>  </td>
                        <td class="td__bold">Photo:</td>
                        <td class="td__bold"><a target="_blank" href="{{URL::to($vehicle->veh_photo) }}"   class="btn btn-danger">Download</a>  </td>
                    </tr>                            
                    
                </tbody>
            </table>

             
        </section>

        <section>
            <br>
            <p style=" text-align:center; font-size:16px; ">Employee Vehicle Use History</p>
        </section>

        <!-- Vehicle Use History -->
        <section class="table__part">           
            <table id="employeeinfo">
                <th>S.N</th>
                <th>Emp ID</th>
                <th>Employee Name</th>
                <th>Iqama No</th>
                <th>Contact No.</th>
                <th>Project</th>
                <th>Assigned Date </th>
                <th>Release</th>
                <th>Status</th>
                <th>Driver License</th>
                <th>Driving Ins.</th>

                @foreach ($driver_veh_records as $record)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$record->dri_emp_id}}</td>
                    <td>{{$record->dri_name}}</td>
                    <td>{{$record->dri_iqama_no}}</td>
                    <td>{{$record->mobile_no }}</td>
                    <td>{{$record->proj_name}}</td>
                    <td>{{$record->assign_date}}</td>
                    <td>{{$record->release_date}}</td>
                    <td>{{$record->status == 1 ? "Running" : "Closed" }}</td>
                    <td> <a target="_blank" href={{asset($record->dri_license_certificate)}}  class="btn btn-danger">Download</a> </td>
                    <td> <a target="_blank" href= "{{ asset($record->dri_ins_certificate) }}"   class="btn btn-danger">Download</a>
                        
                    </td>                    

                </tr>
                @endforeach
                
            </table>         
          
        </section>
       

        

        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Prepared By<br> {{ $report_generated_by }}</p>                    
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
