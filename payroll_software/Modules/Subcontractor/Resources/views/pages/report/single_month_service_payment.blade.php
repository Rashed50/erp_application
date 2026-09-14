<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Subcon-Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 40px 60px 40px;

            @bottom-left {
                content: "Generated on: {{ $current_datetime }}";
                font-size: 8pt;
                color: #666;
            }

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 8pt;
                color: #666;
            }
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 30%;
            left: 15%;
            opacity: 0.05;
            font-size: 80px;
            transform: rotate(-30deg);
            z-index: -1;
        }

        /* Header */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none !important;
        }
        .logo {
            width: 150px;
            height:80px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .company-name small {
            font-size: 14px;
            direction: rtl;
            display: block;
        }
        .label {
            font-weight: bold;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            padding: 3px 12px;
            display: inline-block;
            margin-top: 5px;
        }

        /* Table */
        main {
            margin-top: 5px;
        }



        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;

        }
        #employeeinfo td,th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 8px;
            padding-bottom: 8px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .td__amount, .td__total {
            text-align: right;
            padding-right: 3px;
        }

        .td__total {
            text-align: right;
            padding-right: 3px;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">ASLOOB BEDAA CO.</div>

    <!-- Header -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    /* <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"  alt="Company Logo" class="logo"> */
                    <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">

                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">
                        {{ $company->comp_name_en }}
                        {{-- <small>{{ $company->comp_name_arb }}</small> --}}
                    </div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address }}</p>
                </td>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Print on: {{ $current_datetime }}</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- Table -->
    <main>

         <h3 style="text-align:center; color:black"> Multiple Sponsors Monthly Salary & Payment Summary </h3>

        <table id="employeeinfo">
            <tbody>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Project Name </th>
                        @foreach($sponsors as $sp)

                        <th> {{  $sp->spons_name  }}  </th>
                        @endforeach
                        <th>Total Workers</th>
                        <th>Total Salary</th>
                    </tr>
                </thead>
                    @php
                        $grand_total_emp = 0;
                        $grand_total_amount = 0;
                        $sponsors_base_total_amount = array_fill(0,count($sponsors),0);
                    @endphp

                    @foreach($summary_records as $ar)
                    @php
                      $records = $ar->salary_records;
                      $project_total_amount = 0;
                      $project_total_workers = 0;
                      $counter = 0;

                    @endphp
                    <tr>
                        <td class="td__sn"> {{$loop->iteration}}</td>
                        <td class="td__project">{{$ar->proj_name }}</td>
                        @foreach($records as $ar)
                        @php
                        $project_total_amount +=  $ar->total_gross_salary;
                        $grand_total_amount += $ar->total_gross_salary;

                        $project_total_workers +=  $ar->total_emp;
                        $grand_total_emp +=  $ar->total_emp;

                        $sponsors_base_total_amount[$counter++] += $ar->total_gross_salary;

                        @endphp
                            <td class="td__amount">  {{ $ar->total_gross_salary <= 0 ? '-': number_format( $ar->total_gross_salary , 2, '.', ',') }} </td>
                        @endforeach
                        <td class="td__amount"> {{ $project_total_workers }}</td>
                        <td class="td__total">{{ $project_total_amount <= 0 ? '-':  number_format( $project_total_amount , 2, '.', ',') }} </td>

                    </tr>
                    @endforeach
                    <tr>

                        <td colspan="2" >TOTAL</td>
                        @foreach($sponsors_base_total_amount as $sp)
                            <td  class="td__total"> {{ $sp <= 0 ? '-':  number_format( $sp , 2, '.', ',') }} </td>
                        @endforeach
                        <td class="td__total">{{ $grand_total_emp }}</td>
                        <td  class="td__total">{{ $grand_total_amount <= 0 ? '-':  number_format( $grand_total_amount , 2, '.', ',') }}  </td>

                    </tr>
                </tbody>
        </table>


          <br/>
          <h3 style="text-align:center; color:black"> Multiple Sponsors Payment Summary </h3>

           <table id="employeeinfo">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Subcontractor Name</th>
                    <th>Inserted By</th>
                    <th>Method</th>
                    <th>Month,Year</th>
                    <th>Total Amount</th>
                    <th>Remark</th>

                </tr>
            </thead>
            <tbody>
                 @php

                        $grand_total_amount = 0;
                         $month_name =  date("F", mktime(0, 0, 0, $month, 10));
                @endphp



                @foreach ($payments as $row)
                 @php

                        $grand_total_amount += $row->grand_total;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration  }}</td>
                        <td>{{ $row->subcon_name }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->payment_method ==1 ? "CASH" : "BANK" }}</td>
                        <td> {{ $month_name }}, {{ $year }}</td>
                        <td class="td__amount">{{ number_format($row->grand_total, 2) }}</td>
                        <td class="td__amount">{{ $row->remarks}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="td__amount">Total</td>
                    <td class="td__total">{{ number_format($grand_total_amount, 2) }}</td>
                    <td class="text-right"></td>

                </tr>
            </tfoot>
        </table>

        {{-- Officer Signature --}}
            <div style=" display:flex; justify-content:space-between;margin-top: 100px; ">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>

            </div>


    </main>
</body>
</html>
