<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary- {{ $salary_status}} -{{ $monthName }},{{$salaryYear}}

    </title>
    <!-- style -->
    <style>

        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 99%;
                margin: 0 auto 10px;

            }


            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }

            a[href]:after {
                content: none !important;
            }

          table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }
        /*   End of Page Print Setting */



        /* unvisited link */
        a:link {
            color: red;
        }

        /* visited link */
        a:visited {
            color: green;
        }

        /* mouse over link */
        a:hover {
            color: hotpink;
        }

        /* selected link */
        a:active {
            color: blue;
        }

        .main__wrap {
            width: 90%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
        }

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
            border: 2px solid black;
            border-collapse: collapse;
            font-family: "Franklin Gothic Book", "Times New Roman", "Arial";
        }

        /* table tr {} */

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

        .td__left {

            text-align: left
        }

        .td__center {
            text-align: center
        }

        .td__right {
            text-align: right
        }

        .td__name {
            padding-bottom: 25px;
        }

        .td__bold {
            font-weight: 700;
        }

        .td__emplyoeeId {
            font-size: 14px;
            padding-bottom: 25px;
            color: blue;
            font-weight: 900;
            text-align: center
        }

        .td__sponser {
            color: green;
            font-weight: 300;
            text-align: center
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


        .td__multi__project {
            font-size: 10px;
            padding-right: 20px;
            color: red;
            text-align: right;
        }


        .td__red__color {
            color: red;
            text-align: center;
            font-size: 10px;

        }

        .td__total {
            font-weight: bold;
            text-align: center;
            color: black;
            font-size: 14px;
        }

        .box__signature {
            width: 170px;
            height: 30px;
            border: 1px solid blue;
            margin-top: 5px;
            margin-left: 0px;
            color: gray;
        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }


/*
         #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 2px solid #ddd;
            padding: 8px;
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

        } */


    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="date__part">
                <p> Salary of   {{ $monthName }}, {{$salaryYear}}  </p>
                <p>  Salary Status  {{ $salary_status}} </p>
                <p>  Project:  <br> {{ $project}} </p>

            </div>
            <!-- title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
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
        <!-- table part -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th colspan = "13" style="text-align: center;" >{{ $monthName }}, {{$salaryYear}}, {{ $project}}  </th>
                    </tr>
                    <tr>
                        <th>SL<br>No </th>
                        <th> Emp. <br> ID </th>
                        <th> Employee Name </th>
                        <th> Iqama No<br><br> BS/Rate </th>
                        <th>Sponsor <br><br>Basic Hrs. </th>
                        <th>Nat <br><br> OT/Amt</th>
                        <th>Working <br>Days  <hr> P &nbsp; &nbsp; L</th>
                        <th> Trade <br> <br> (Food+Other) </th>
                        <th> <br>Total <br> Salary</th>
                        <th style="text-align: right;"> Deduction  <br> <hr> (S.T+Adv.+C.D) </th>
                        <th style="width:50px;white-space: normal;"><br><hr>ID+P.Paid</th>
                        <th> <br> Net <br> Salary </th>
                        <th> Project <br> Signature </th>
                    </tr>
                </thead>


                 <tbody>

                    @php
                    $totalHours = 0;
                    $totalOverTimeHours = 0;
                    $totalOverTimeAmount = 0;
                    $totalFoodAllowance = 0;
                    $ground_total_others_amount = 0;
                    $totalFoodDeduction = 0;
                    $total_slh_all_include_amount = 0;
                    $totalSaudiTax = 0;
                    $totalContribution = 0;
                    $totalOtherAdvance = 0;
                    $totalIqamaRenewal = 0;
                    $totalSalaryAmount = 0;
                    @endphp


                    @foreach($salaryReport as $salary)

                    {{-- find multiple project --}}
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
                    $total_slh_all_include_amount += $salary->slh_all_include_amount;

                    $total_others_amount = $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount;
                    $ground_total_others_amount += $total_others_amount;
                    $multi_pro_total_hour = "";
                    $multi_project = "";
                    $findMultipleProjectEmployee = App\Models\EmployeeMultiProjectWorkHistory::where('emp_id',$salary->emp_auto_id)->where('month',$salary->slh_month)->where('year',$salary->slh_year)->get();
                    @endphp

                    <tr style="border-bottom:0;">
                        <td class="td__center td__name">{{ $loop->iteration }}</td>
                        <td class="td__center td__emplyoeeId"> {{ $salary->employee_id }} </td>
                        <td class="td__left td__name"> <span>{{ $salary->employee_name }}</span> </td>

                        <td class="td__center"> {{ $salary->akama_no }} <br><br> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}

                        </td>
                        <td class="td__sponser"><span class="sponser__name"> {{Str::limit( $salary->spons_name,12)}}</span> <br> {{ $salary->slh_total_hours }} </td>
                        <td class="td__center"> <span class="country"> {{ Str::limit($salary->country_name,3) }}</span> <br><br>{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>

                        <td  class="td__sponser">{{ $salary->slh_total_working_days - $salary->paid_leave  }} &nbsp;&nbsp;{{ $salary->paid_leave }} <br><br> {{ $salary->slh_total_working_days  }} </td>

                        <td class="td__center"> <span class="employe__trade"> {{ Str::limit($salary->catg_name,10) }}</span> <br><br>
                            {{round($salary->food_allowance) }} + {{ round($total_others_amount) }}
                             {{-- {{ $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount }} --}}
                        </td>
                        <td class="td__gross_salary"><br> {{round( $salary->slh_all_include_amount) }}
                        <!--{{round( $salary->slh_total_salary + $salary->slh_cpf_contribution + $salary->slh_iqama_advance + $salary->slh_saudi_tax +$salary->slh_other_advance) }} -->
                        </td>

                        <td class="td__center" style="text-align: right;"> <br>{{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}
                        </td>
                        <th><br>{{round($salary->slh_iqama_advance)}} <br>{{ round($salary->partial_paid_amount) }} </th>
                        <td class="td__gross_salary"> <br>{{ round($salary->slh_total_salary )}}</td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br>
                           <div class="box__signature">
                                @if($salary->Status == 0)
                                        @if($salary->salary_status >= 2)
                                        <b> Salary Hold </b> <br>
                                        {{ $salary->title }} {{-- Employee job status     --}}
                                        @endif
                                        <br>Unpaid
                                @else
                                    <br>{{  $salary->slh_paid_method == true ? 'Paid by Bank':'Paid by Cash'}}
                                 @endif

                            </div>
                        </td>
                    </tr>



                    @if($findMultipleProjectEmployee->count()>1)
                    @foreach($findMultipleProjectEmployee as $multiData)
                    @php
                    $multi_pro_total_hour = $multi_pro_total_hour.($multiData->total_hour."/".$multiData->total_day)."<br>";
                    $multi_project = $multi_project.$multiData->projectName->proj_name."<br>";
                    @endphp
                    @endforeach
                    <tr style="border:0; padding:0">
                        <td colspan="4"></td>
                        <td class="td__center">{!! $multi_pro_total_hour !!}</td>
                        <td class="td__center"> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td colspan="3" class="td__multi__project">{!! $multi_project !!}</td>
                    </tr>

                    @endif


                    @endforeach

                    {{-- match with table border color and hide column --}}
                    <tr >
                        <td class="td__total" colspan="4">Total <br><br> {{ "" }}</td>
                        <td class="td__total">Basic Hrs <br><br> {{ $totalHours }} </td>
                        <td class="td__total"> O.T+Amount <br>{{$totalOverTimeHours}} <br>{{ round($totalOverTimeAmount) }}</td>
                        <td></td>
                        <td class="td__total">Food+Other <br> {{round($totalFoodAllowance) }} <br> {{ round( $ground_total_others_amount) }}</td>
                        <td class="td__total">Total <br>Salary <br>{{ round($totalSalaryAmount + $totalSaudiTax + $totalIqamaRenewal +$totalOtherAdvance +$totalContribution + $totalFoodDeduction) }}</td>
                        <td class="td__total">S.T+Adv+C.D<br>{{ round($totalSaudiTax) }}+ {{ round($totalOtherAdvance) }} <br>+ {{ round($totalFoodDeduction) }}</td>
                        <td class="td__total">ID <br> Renew <br> {{ round($totalIqamaRenewal) }} </td>
                        <td class="td__total"> Net <br>Salary <br> {{ round($totalSalaryAmount) }} </td>
                        <td class="td__project"> {{""}} <br> {{""}} </td>
                    </tr>

                </tbody>
            </table>
        </section>



         </section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; text-align: center; ">
                    <p>  <b>  ------------------------ <br> {{$login_name}} </b> <br> Prepared By  </p>
                    <p>  <b>  ------------------------ <br>   </b> Accounts/HR  </p>
                    <p>  <b>  ------------------------ <br>   </b> Approved By  </p>
                </div>
            </div>
            <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p>
        <section>
            <!-- ---------- -->
    </div>
</body>

</html>
