<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Inv.</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* The key CSS for the header */
        header {
            position: fixed;
            top: -50px; /* Adjust as needed */
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            /* line-height: 35px; */
            font-size: 14px;
        }

        /* The key CSS for the footer */
        footer {
            position: fixed;
            bottom: -50px; /* Adjust as needed */
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 12px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            padding-bottom: 10px;

        }

        /* Adjust content margins to prevent overlap */
        .content {
            padding-left: 20px;
            padding-right: 10px;
            padding-top: 50px;
            margin-top: 40px;    /* Slightly more than header height */
            margin-bottom: 10px; /* Slightly more than footer height */
        }

        h1, h2, p {
            margin: 0 0 10px 0;
        }



    .header-table {
         width: 90%;
         margin-bottom: 12px;
         border-collapse: collapse;
         padding-left: 40px;
         padding-right: 30px;
         padding-top:10px;
         padding-bottom: 10px;
         border-radius: 3px;
         margin: 0;
      }

      .header-table td {
         vertical-align: middle;
      }

      .company-title {
         font-size: 14px;
         font-weight: bold;
         margin-bottom: 4px;
         color: #222;
         text-align: right;
      }

      .subtitle {
         font-size: 11px;
         color: #666;
            text-align: right;
      }
       .content-hr {
         border: 0;
         border-top: 2px dotted #bbb;
         margin: 20px 0;
      }

    .watermark {
         position: fixed;
         top: 40%;
         left: 15%;
         opacity: 0.09;
         font-size: 70px;
         transform: rotate(-30deg);
         z-index: -1;
      }

      .top-hr {
         border: 0;
         border-top: 3px solid lightblue;
         margin: 0;
      }

        #employeeinfo {
            font-family: 'Time New Roman', Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
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
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
            /*text-align: center;*/

        }



    </style>
</head>
<body>

    <!-- Watermark -->
   <div class="watermark">ASLOOB BEDAA CO.</div>
    <header>
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 70%;">
                    <!--<img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"-->
                    <!--style="width: 130px; height: auto;">-->
                     <img src="{{ asset($company->com_logo) }}"  alt="logo not found" width="184px" height="80px">
                </td>
                <td style="width: 30%; text-align: end;">
                    <div class="company-title">ASLOOB BEDAA CO.</div>
                    <div class="subtitle">12611, {{ $company->comp_address }}</div>
                    <div class="subtitle">+966 54 765 7236, {{ $company->comp_phone1 }} </div>
                    <div class="subtitle">{{ $company->comp_email1 }} </div>
                    <div style="text-align: right;font-size:10px;"> Printed at:{{ $current_datetime  }} </div>

                </td>
            </tr>

        </table>

        <hr class="top-hr">
    </header>

    <div class="content">
        <h2 style="text-align: center; text-decoration: underline; font-size:14px; margin-bottom:10px;padding-top:0px; margin-top:0px;"> Employee Details Report </h2>
        <table id="employeeinfo">
                <thead>
                    <tr>
                    <th > <span>S.N</span> </th>
                    <th> <span>ID</span> </th>
                    <th> <span>Employee Name</span> </th>
                    <th>Passport No</th>
                    <th> <span>Iqama No</span> </th>
                    <th> <span>Nat.</span> </th>
                    <th> <span>Project Name</span> </th>
                    <th> <span>Designation</span> </th>
                    <th> <span>Sponsor</span> </th>
                    <th>Joining At</th>
                    <th>Current Salary</th>
                    {{-- <th colspan="2"> <span>Download</span> </th> --}}
                    </tr>
                </thead>
                <tbody>
                @foreach ($employees as $emp)
                <tr >
                    <td class="td__s_n"> {{ $loop->iteration }}</td>
                    <td class="td__employee_id">{{$emp->employee_id}} </td>
                    <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                    <td class="td__iqama"> {{ $emp->passfort_no }} </td>
                    <td class="td__iqama"> {{ $emp->akama_no }} </td>
                    <td class="td__country"> {{ Str::limit($emp->country_name,3) }}</td>
                    <td class="td__emplyoee_info"> {{ $emp->proj_name }} </td>
                    <td class="td__emp_trade"> {{Str::limit( $emp->catg_name,15) }} </td>
                    <td class="td__emplyoee_info"> {{ Str::limit( $emp->spons_name ,15)}}</td>
                    <td class="td__employee_id"> {{  date('d-m-Y', strtotime($emp->joining_date)) }} </td>
                    @php
                        $salary_amount =   $emp->hourly_employee  == 1 ?  $emp->hourly_rent: ($emp->basic_amount + $emp->house_rent + $emp->mobile_allowance + $emp->medical_allowance + $emp->local_travel_allowance + $emp->others1);
                    @endphp
                    <td class="td__employee_id"> {{ $emp->hourly_employee == null ? "Basic-". $salary_amount: 'Hourly-'.$salary_amount }} </td>
                    {{-- <td class="td__iqama">
                        @if($emp->akama_photo)
                        <a href="{{ route('employee.iqama.file.download.request',[$emp->employee_id]) }}" id="print_button"><i class="fa fa-pencil-square fa-lg edit_icon">Iqama</i></a>
                        @endif
                    </td>
                    <td class="td__iqama">
                        @if($emp->pasfort_photo)
                        <a href="{{ route('employee.passport.file.download.request',[$emp->employee_id]) }}" id="print_button"><i class="fa fa-pencil-square fa-lg edit_icon">Passport</i></a>
                        @endif
                    </td> --}}
                 </tr>
                @endforeach
              </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; {{ date('Y') }} Asloob Bedaa Contracting Company All rights reserved.</p>
        <p>Contact us at  {{ $company->comp_address }}, {{ $company->comp_phone1 }},{{ $company->comp_email1 }} </p>
    </footer>
</body>
</html>
