<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Hold Emp  </title>
    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 0 auto 20px;

            }


            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */
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
            margin: 20px auto;
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
            padding: 10px;
        }

        table,
        tr {
            border: 1px solid #E0E0E0;
            border-collapse: collapse;
            font-family: "Franklin Gothic Book", "Times New Roman", "Arial";
        }

        /* table tr {} */

        table th {
            font-size: 13px;
        }

        table td {
            text-align: left;
            font-size: 13px;

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
            color: gray;
            font-weight: 100;
            text-align: center;

        }


        .td__multi__project {
            font-size: 10px;
            color:gray;
            text-align: center
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
            width: 200px;
            height: 30px;
            border: 1px solid gray;
            margin-top: 5px;
            padding-top: 5px;
            margin-left: 0px;
            color: red
        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }
    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="date__part">
                <p><strong> Salary Month of {{ $month_name }} {{$year}} </strong> </p> 
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
        <h5 style="text-align: center; padding-bottom:10px;">Salary Hold Employees Salary Report </h5>
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th colspan ="11" class="td__center" >Salary month of {{ $month_name }} {{$year}} </th>                        
                    </tr>
                    <tr>
                        <th>SL<br>No </th>
                        <th> Employee <br> ID </th>
                        <th> Employee Name </th>
                        <th> Iqama No<br> BS/Rate </th>
                        <th>Sponor <br>Hr/WrD </th>
                        <th>Nation <br> OT/Amt</th>
                        <th> Trade <br> (Food+Other) </th>
                        <th> <br>Total Salary</th>
                        <th> <br>S.T+Adv+C.D </th>
                        <th> <br>Contribution<br>ID Renew</th>
                        <th> <br> Gross Salary </th>
                        <th> Project <br> Signature </th>
                    </tr>
                </thead>
                <tbody>

                    @php
                    $totalHours = 0;
                    $totalOverTimeHours = 0;
                    $totalOverTimeAmount = 0;
                    $totalFoodAllowance = 0;
                    $totalSaudiTax = 0;
                    $totalContribution = 0;
                    $totalOtherAdvance = 0;
                    $totalIqamaRenewal = 0;
                    $totalSalaryAmount = 0;
                        $totalFoodDeduction = 0;
                    $total_slh_all_include_amount = 0;
                    @endphp


                    @foreach($salary_records as $salary)

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
                        <td class="td__sponser"><span class="sponser__name"> {{Str::limit( $salary->sponser->spons_name,15)}}</span> <br><br> {{ $salary->slh_total_hours }}/{{ $salary->slh_total_working_days }} </td>
                        <td class="td__center"> <span class="country"> {{ Str::limit($salary->country->country_name,3) }}</span> <br><br>{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>

                        <td class="td__center"> <span class="employe__trade"> {{ Str::limit($salary->category->catg_name,10) }}</span> <br><br> 
                            {{round($salary->food_allowance) }}
                            + {{ $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount }}
                        </td>
                             
                         <td class="td__gross_salary"><br><br> {{round( $salary->slh_all_include_amount) }}
                         </td>
                        
                        <td class="td__center"><span class="td__red__color"> {{ "" }} </span> <br><br> {{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}
                        </td>

                        <td class="td__center"> <br><br> {{ round($salary->slh_cpf_contribution)}}/{{round( $salary->slh_iqama_advance) }} </td>
                        <td class="td__gross_salary"> <br><br><u> {{ $salary->slh_total_salary }} </u> </td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br>
                            <div class="box__signature">
                                <b>{{$salary->title}}, </b>  
                                <b>Salary Hold, </b>
                            
                                <b>{{  $salary->Status == 0 ? 'Unpaid':'Paid'}}</b>      
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
                        <td></td>
                        <td></td>
                        <td> </td>
                        <td></td>
                        <td class="td__center">{!! $multi_pro_total_hour !!}</td>
                        <td class="td__center"> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td class="td__multi__project">{!! $multi_project !!}</td>
                    </tr>
                    @endif
                    @endforeach
 
                    <tr style="border-bottom:0;">
                        <td class="td__center td__name"></td>
                        <td class="td__center td__emplyoeeId"> {{ "" }} </td>
                        <td class="td__left td__name"> <span>{{ "" }}</span> </td>

                        <td class="td__total">Total <br><br> {{ "" }}</td>
                        <td class="td__total">Hours <br><br> {{ $totalHours }} </td>
                        <td class="td__total"> O.T/Amnt <br><br>{{$totalOverTimeHours}}/{{ $totalOverTimeAmount }}</td>
                        <td class="td__total">Food Allo. <br><br> {{$totalFoodAllowance }}</td>
                        <td class="td__total">Total Salary <br><br> {{ $total_slh_all_include_amount }}
                        <!--{{ $totalSalaryAmount + $totalSaudiTax + $totalIqamaRenewal +$totalOtherAdvance +$totalContribution+$totalFoodDeduction }}-->
                        </td>
                        
                        <td class="td__total">S.T+Adv+C.D<br>{{ $totalSaudiTax }}+{{ $totalOtherAdvance }} <br>+{{ $totalFoodDeduction }}
                        </td>

                        <td class="td__total">Iqama Renew <br> {{ $totalIqamaRenewal}} </td>
                        <td class="td__total"> Salary <br><br> {{ $totalSalaryAmount }} </td>
                        <td class="td__project"> {{""}} <br> {{""}} </td>
                    </tr>

                </tbody>
            </table>
        </section>



        </section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
        <section>
            <!-- ---------- -->
    </div>
</body>

</html>