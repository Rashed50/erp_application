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
            font-size: 8px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }

        .td__gross_salary {
            font-size: 13px;
            color: navy;
            font-weight: 900;
            text-align: center
        }
         .td__balance_table_gross_salary {
            font-size: 13px;
            color: navy;
            font-weight: 900;
            padding-right: 5px;
            text-align: Right;
        }

        .box__signature {
            width: 150px;
            height: 30px;
            border: 1px solid gray;
            margin: 0px;
            color: lightgray;
        }

        .final_table {
            width: 70%;
            margin-left: auto;
            margin-right: auto;

        }

        .final_table tr {
            border: 0px;
        }

        .table__part {
            display: flex;
        }


        #emp_details_table{
            font-family: Arial, Helvetica, sans-serif;
            /* 1. Makes borders and spacing collapse into one */
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

             /* 1. Makes borders and spacing collapse into one */
            border-collapse: collapse;
            /* 2. Removes the space between cells in the separate border model (though 'collapse' often handles this) */
            border-spacing: 0;
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
               <p> Print:<br> {{ Carbon\Carbon::parse( Carbon\Carbon::now())->format('d-m-Y h:i A')  }}  </p>
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
               <button type="" onclick="showEmployeActivityRecord()" class="print__button">Activities Details</button>
                &nbsp; &nbsp; &nbsp; &nbsp;
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
                            , {{$empl_last_activity}},{{ $employee->salary_status == 1 ? 'Salary: Active' : 'Salary: Hold' }}
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
        <section>
            <br>
        </section>


        <!-- Salary all records part -->
        <section class="table__part">
            <table id="salary_table">
                <thead>

                    <tr>
                        <td colspan="13" style="text-align: center;background:#B3E6FA;padding-top:5px;padding-bottom:5px;font-size: 13px;font-weight: bold;">
                            Salary Summary of  ID {{ $employee->employee_id}}, {{$employee->employee_name}}
                        </td>
                    </tr>
                     <tr style="">
                        <th>S.N </td>
                        <th> Month, Year </th>
                        <th> B.Salary <br>Hourly </th>
                        <th>Working <br>Days  <hr> P &nbsp; &nbsp; L</th>
                        <th>Hours/Days </th>
                        <th> O.T/Amount</th>
                        <th> Food<br> +Other </th>
                        <th> Total <br>Salary </th>
                        <th style="text-align: right;"> Deduction  <br> <hr> (S.T+Adv.+C.D) </th>
                        <th  style="width:48px;white-space: normal;"> <br> <hr>ID+P.Paid</th>
                        <th> Net <br> Salary </th>
                        <th> Project </th>
                        <th>Payment Status <br> <br> Signature </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalHours = 0;
                    $totalOverTimeHours = 0;
                    $totalOverTimeAmount = 0;
                    $totalFoodAllowance = 0;
                    $total_others_amount = 0;
                    $totalSaudiTax = 0;
                    $totalContribution = 0;
                    $totalOtherAdvance = 0;
                    $totalIqamaRenewal = 0;
                    $totalSalaryAmount = 0;
                    $totalFoodDeduction = 0;
                    $all_include_amount = 0;
                     $totalPartialAmnt = 0;

                    @endphp

                    @foreach($salary_records as $salary)

                    @php
                    $totalHours += $salary->slh_total_hours;
                    $totalOverTimeHours += $salary->slh_total_overtime;
                    $totalOverTimeAmount += $salary->slh_overtime_amount;
                    $totalFoodAllowance += $salary->food_allowance;
                    $totalIqamaRenewal += $salary->slh_iqama_advance;
                    $totalSaudiTax += $salary->slh_saudi_tax;
                    $totalContribution += $salary->slh_cpf_contribution;
                    $totalOtherAdvance += $salary->slh_other_advance;
                    $totalSalaryAmount += $salary->slh_total_salary;
                    $totalFoodDeduction  += $salary->slh_food_deduction;

                    $totalPartialAmnt += $salary->partial_paid_amount;


                    $all_include_amount += $salary->slh_all_include_amount > 0 ?  $salary->slh_all_include_amount : ($salary->slh_total_salary + $salary->slh_cpf_contribution + $salary->slh_iqama_advance + $salary->slh_saudi_tax + $salary->slh_other_advance);
                    $others_amount_all_included = $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount;
                    $total_others_amount +=  $others_amount_all_included;

                    // only for paid leave calculation
                    $monthly_work_record = App\Models\MonthlyWorkHistory::where('emp_id',$salary->emp_auto_id)->where('month_id',$salary->slh_month)->where('year_id',$salary->slh_year)->where('work_project_id',$salary->project_id)->first();
                    $salary->paid_leave =  $monthly_work_record ? $monthly_work_record->paid_leave:0;

                    @endphp

                     <tr style="border-bottom:0;">
                        <td class="td__center">{{ $loop->iteration }}</td>
                        <td class="td__month_year"> {{ date('F', mktime(0, 0, 0, $salary->slh_month, 10)); }}, {{ $salary->slh_year }} </td>
                        <td class="td__center"> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}</td>
                        <td  class="td__center">{{ $salary->slh_total_working_days - $salary->paid_leave  }} &nbsp;&nbsp;{{ $salary->paid_leave }} </td>
                        <td class="td__sponser"> {{ $salary->slh_total_hours }} /{{ $salary->slh_total_working_days}} </td>
                        <td class="td__center"> {{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>
                        <td class="td__center"> {{round($salary->food_allowance) }} + {{ round($others_amount_all_included) }}  </td>
                        <td class="td__gross_salary">  {{
                          $salary->slh_all_include_amount > 0 ?  round( $salary->slh_all_include_amount) : round( $salary->slh_total_salary + $salary->slh_cpf_contribution + $salary->slh_iqama_advance + $salary->slh_saudi_tax + $salary->slh_other_advance)
                            }}
                        </td>
                         <td class="td__center" style="text-align: right;">{{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}
                        </td>
                        <td class="td__center"> {{ round($salary->slh_iqama_advance) }}  <br>{{ round($salary->partial_paid_amount) }}</td>
                        <td class="td__gross_salary"> {{number_format( round($salary->slh_total_salary),2) }} </td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,20) }}<br></td>
                        <td class="td__gross_salary">
                            <div class="box__signature">
                                 {{ $salary->Status == 0 ? 'Unpaid': ( $salary->slh_paid_method == null ? "Paid by Cash" : "Paid by Bank") }}
                            </div>

                        </td>

                    </tr>


                    @endforeach
                     <tr class="t__total_row">
                        <td></td>
                         <td class="td__gross_salary">Total </td>
                        <td class="td__gross_salary"> </td>
                        <td class="td__gross_salary"> </td>
                        <td class="td__gross_salary">Basic Hrs <br><br>{{ number_format($totalHours,2) }} </td>
                        <td class="td__gross_salary">O.T+Amount <br>{{ number_format( $totalOverTimeHours,2)}} <br>{{ number_format( $totalOverTimeAmount,2) }}</td>
                        <td class="td__gross_salary">Food+Other <br>  {{  round($totalFoodAllowance)  }}  <br> {{ round($total_others_amount) }}   </td>
                        <td class="td__gross_salary">Total <br>Salary <br>{{ round($totalSalaryAmount + $totalSaudiTax + $totalIqamaRenewal +$totalOtherAdvance + $totalFoodDeduction ) }}</td>
                        <td class="td__gross_salary">S.T+Adv+C.D<br> {{round($totalSaudiTax)}}+ {{  round($totalOtherAdvance)}} + {{  round($totalFoodDeduction)}} </td>
                        <td class="td__gross_salary"> ID+P.Paid<br>  {{  round($totalIqamaRenewal) }} <br> {{  round($totalPartialAmnt) }} </td>
                        <td class="td__gross_salary"> Net <br>Salary <br>{{number_format( round($totalSalaryAmount),2) }} </td>
                        <td class="td__gross_salary"> </td>
                        <td class="td__gross_salary"> </td>
                    </tr>

                </tbody>
            </table>
        </section>


        <!-- Bonus Salary Records -->
        <section class="table__part">
           @if($bonus_records->count() >0)
            <table id="employeeinfo">
                <thead>
                    <tr> <td colspan="7" style="text-align: center;background:rgb(138, 219, 118, 0.46);padding-top:5px;padding-bottom:5px;font-size: 13px;">Bonus Details of {{ $employee->employee_id}}, {{$employee->employee_name}} </td></tr>
                    <tr>
                        <th>S.N </th>
                        <th>Month</th>
                        <th> Year </th>
                        <th> Bonus Type </th>
                        <th> Date </th>
                        <th> Amount </th>
                        <th> Remarks </th>
                    </tr>
                </thead>
                <tbody>


                    @foreach($bonus_records as $arecord)

                    <tr>
                        <td class="td__center">{{ $loop->iteration }}</td>
                        <td class="td__gross_salary"> {{ date('F', mktime(0, 0, 0, $arecord->month, 10)); }}  </td>
                        <td class="td__gross_salary"> {{ $arecord->year }} </td>
                        <td class="td__center"> {{ $arecord->bonus_type->name}}</td>
                        <td class="td__center"> {{ $arecord->updated_at }} </td>
                        <td class="td__center"> {{ $arecord->amount }} </td>
                        <td class="td__center"> {{ $arecord->remarks }} </td>
                    </tr>
                    @endforeach


                </tbody>
            </table>
            @endif
        </section>


        <!-- Iqama Renewal Details part -->
        <section>
            <br>
        </section>
        <section>
            <table id="salary_table">
                <thead>
                    <tr>
                        <td colspan="13"
                            style="text-align: center;background:rgba(223, 80, 37, 0.275);padding-top:5px;padding-bottom:5px;font-size: 13px;font-weight: bold;">
                            Iqama Renewal Details of  {{ $employee->employee_id}} ,{{$employee->employee_name}}
                        </td>
                    </tr>

                    <tr>
                        <th  >S.N</th>
                        <th >Renew Date</th>
                        <th  >Duration</th>
                         <th>Expire AT</th>
                        <th  >Jawazat</th>
                        <th  >Maktab Amal</th>
                        <th  >VISA-Amount</th>
                        <th  >Medical Ins.</th>
                        <th  >JZT Penalty</th>
                        <th  >Other</th>
                        <th  >Total</th>
                        <th  >Paid By</th>
                        <th >Note</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $counter = 1;
                    $totalIqamaExpence = 0;
                    $total_jawazat_fee = 0;
                    $total_maktab_alamal_fee = 0;
                    $total_bd_amount = 0;
                    $total_medical_insurance = 0;
                    $total_jawazat_penalty = 0;
                    $total_others_fee = 0;
                     $total_renewal_duration = 0;
                     $iqama_renewal_total_month = 0;
                    @endphp

                    @foreach($iqamaExpenseAllRecords as $item)
                    @php
                        $totalIqamaExpence += $item->jawazat_fee + $item->maktab_alamal_fee +$item->bd_amount +$item->medical_insurance + $item->others_fee + $item->Cost6 + $item->jawazat_penalty;
                    $total_jawazat_fee += $item->jawazat_fee;
                    $total_maktab_alamal_fee += $item->maktab_alamal_fee;
                    $total_bd_amount += $item->bd_amount;
                    $total_medical_insurance += $item->medical_insurance;
                    $total_jawazat_penalty += $item->jawazat_penalty;
                    $total_others_fee += $item->others_fee + $item->Cost6;
                    $total_renewal_duration +=  $item->duration;
                    $iqama_renewal_total_month += $item->payment_purpose_id == 1 ? $item->duration : 0;

                    @endphp
                    <tr>
                        <td class="td__center">{{$counter++}}</td>
                        <td class="td__center"> {{  \Carbon\Carbon::parse($item->renewal_date)->format('d-m-Y') }} </td>
                        <td class="td__center"> {{ $item->duration }} Months </td>
                        <td class="td__center"> {{ $item->iqama_expire_date }} </td>
                        <td class="td__center"> {{ $item->jawazat_fee }} </td>
                        <td class="td__center"> {{ $item->maktab_alamal_fee }} </td>
                        <td class="td__center"> {{ $item->bd_amount }} </td>
                        <td class="td__center"> {{ $item->medical_insurance }} </td>
                        <td class="td__center"> {{ $item->jawazat_penalty }} </td>
                        <td class="td__center"> {{ $item->others_fee }} </td>
                        <td class="td__center"> {{ $item->total_amount }} </td>
                        <td class="td__center"> {{ $item->expense_paid_by == 2 ? 'Company' : 'Self' }} </td>
                        <td class="td__center"> {{ $item->remarks }} </td>

                    </tr>
                    @endforeach
                     <tr class="t__total_row">
                        <td class="td__center" colspan="2"> Total</td>
                        <td class="td__center" colspan="2">Iqama Renewal  {{ $iqama_renewal_total_month }} Months </td>
                        {{-- <td></td> --}}
                        <td class="td__center"> {{ $total_jawazat_fee }} </td>
                        <td class="td__center"> {{ $total_maktab_alamal_fee }} </td>
                        <td class="td__center"> {{ $total_bd_amount }} </td>
                        <td class="td__center"> {{ $total_medical_insurance }} </td>
                        <td class="td__center"> {{ $total_jawazat_penalty }} </td>
                        <td class="td__center"> {{ $total_others_fee }} </td>
                        <td class="td__center"> {{ $totalIqamaExpence }}  </td>

                        <td class="td__center"  colspan="2">   </td>

                    </tr>

                </tbody>
            </table>
        </section>
        {{-- summary information details  --}}
        <section>
            <br>


             <table id="employeeinfo" style="width: 70%; margin-left: auto; margin-right: auto; font-size:13px; ">
                <tbody>

                    @php

                        $iqama_balance = $iqamaRenewalTotalExpence  - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount ); // iqama balnnce
                        $advance_balance =  $otherAdvanceTotalAmount - $total_other_advace_deduction_from_salary;
                        $final_balance = $iqama_balance + $advance_balance;

                    @endphp

                     <tr>
                        <td colspan="4"
                            style="text-align: center;background:rgb(40, 120, 185);padding-top:5px;padding-bottom:5px;font-size: 13px;font-weight: bold;">
                            Final Settlement Summary - Employee Account Statement
                        </td>
                    </tr>

                     <tr style=" background-color: #f2f2f2;">
                        <td class="sm_td_header" > </td>
                        <td class="td__balance_table_gross_salary">  </td>
                        <td class="sm_td_header">Total Unpaid Salary</td>
                        <td class="td__balance_table_gross_salary">{{number_format(  round($totalUnPaidSalaryAmount),2) }} </td>

                    </tr>

                    <tr>
                         <td class="sm_td_header">Iqama Renewal Expense</td>
                        <td class="td__balance_table_gross_salary">{{number_format( round($iqamaRenewalTotalExpence),2) }} </td>
                        <td class="sm_td_header"  >Advance Taken</td>
                        <td class="td__balance_table_gross_salary"  > {{number_format( $otherAdvanceTotalAmount ,2)}} </td>
                    </tr>
                    <tr  style=" background-color: #f2f2f2;">

                        <td class="sm_td_header">Iqama Deduction from Salary </td>
                        <td class="td__balance_table_gross_salary">{{number_format( round( $toal_iqama_expense_deduction_from_salary),2) }} </td>
                        <td class="sm_td_header"> Advance Deduction From Salary</td>
                        <td class="td__balance_table_gross_salary"> {{number_format(round(  $total_other_advace_deduction_from_salary ) ,2)}} </td>

                    </tr>
                    <tr>
                        <td class="sm_td_header">Self Contribution/Cash Received</td>
                        <td class="td__balance_table_gross_salary"> {{number_format($cashReceiveTotalPaidAmount,2) }}</td>
                        <td class="sm_td_header"> Advance Balance</td>
                        <td class="td__balance_table_gross_salary"> {{number_format( $otherAdvanceTotalAmount - round(  $total_other_advace_deduction_from_salary ),2) }} </td>
                    </tr>

                    <tr  style=" background-color: #f2f2f2;">

                        <td class="sm_td_header">Total Received (From Employee)</td>
                        <td class="td__balance_table_gross_salary">{{number_format( round( $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount ),2) }} </td>
                        <td class="sm_td_header">Iqama Outstanding Balance</td>
                        <td class="td__balance_table_gross_salary"> {{ number_format(round(  ($iqamaRenewalTotalExpence) -   ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount) ),2) }} </td>
                    </tr>
                   <tr  style=" background-color:rgba(197, 171, 87, 0.932); color:navy; font-weight:bold;">
                         <td class="sm_td_header" style="text-align: center;" colspan="3">
                            Final Settlement Amount
                            @if ($final_balance < 0)
                             (Payable to Employee)
                            @else
                            ( Receivable from Employee)
                            @endif
                        </td>
                        <td class="td__balance_table_gross_salary">{{ number_format(round( ($final_balance < 0 ? $final_balance *-1 : $final_balance )),2) }} </td>
                    </tr>
                    <tr>
                        <td class="sm_td_header" style="text-align: center;" colspan="4">Employee of {{ $employee->employee_id}} ,{{$employee->employee_name}} </td>
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

       function showEmployeActivityRecord(){
        var emp_id = document.getElementById('emp_id').innerHTML;

        var url = "{{ route('anemployee.activities.details.report', ':parameter') }}";
        url = url.replace(':parameter', emp_id);
        window.open(url, '_blank');

   }
</script>

</html>
