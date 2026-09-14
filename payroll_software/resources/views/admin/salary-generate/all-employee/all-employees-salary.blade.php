<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report</title>

   <style>
    * {
        margin: 0;
        padding: 0;
        outline: 0;
        box-sizing: border-box;
    }

    /* Watermark container */
    .watermark-container {
        position: relative;
        background: white;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Angled watermark */
    .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        z-index: 999;
        opacity: 0.12;
        pointer-events: none;
        font-size: 70px;
        font-weight: bold;
        color: #000;
        text-transform: uppercase;
        white-space: nowrap;
        font-family: Arial, sans-serif;
        letter-spacing: 3px;
    }

    /* Print styles */
    @media print {
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 999;
            opacity: 0.12;
            pointer-events: none;
            font-size: 70px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            white-space: nowrap;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 landscape;
            margin: 7mm 2mm 12mm 10mm; /* Increased bottom margin for footer */
        }

        .print__button {
            display: none;
        }

        a[href]:after {
            content: none !important;
        }

        table {
            page-break-after: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        td {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }

        /* Footer for print - appears on EVERY page */
        .footer {
            position: running(footer);
            text-align: center;
            font-size: 8px;
            color: #666;
            padding: 3px 0;
            width: 100%;
            bottom: 0;
        }


        @page {
            /* Compact Footer - Appears on every page */
            @bottom-center {
                content: "Online System Generated Statement Authorized by Accounts Department";
                font-size: 8px;
                color: #666;
            }
        }
    }

    /* Screen styles */
    @media screen {
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 999;
            opacity: 0.1;
            pointer-events: none;
            font-size: 70px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            white-space: nowrap;
        }
    }

    .main__wrap {
        width: 98%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
        background: white;
        flex: 1;
    }

    .header__part {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .title__part {
        text-align: center;
    }

    .table__part {
        display: flex;
        width: 100%;
    }

    table {
        width: 100%;
        padding: 0;
        border: 2px solid black;
        border-collapse: collapse;
        font-family: "Franklin Gothic Book", "Times New Roman", "Arial";
    }

    table tr {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    table th, table td {
        border-left: none !important;
        border-right: none !important;
        padding: 4px 2px;
        vertical-align: middle;
    }

    table th {
        font-size: 12px;
    }

    table td {
        text-align: left;
        font-size: 12px;
        padding: 2px 1px;
    }

    th, td {
        border-left: 1px solid black;
    }

    th:first-child, td:first-child {
        border-left: none;
    }

    /* Footer Styles - Compact */
    .footer {
        text-align: center;
        padding: 4px 0;
        margin-top: 8px;
        font-size: 8px;
        color: #888;
        border-top: 1px solid #ddd;
        font-family: "Franklin Gothic Book", "Times New Roman", "Arial";
        background: white;
        line-height: 1.2;
    }

    .footer p {
        margin: 0;
        padding: 0;
    }

    /* For screen - footer at bottom */
    @media screen {
        .watermark-container {
            min-height: 100vh;
        }

        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
        }
    }

    /* Helper classes */
    .td__left { text-align: left }
    .td__center { text-align: center }
    .td__right { text-align: right }
    .td__name { padding-bottom: 25px; }
    .td__bold { font-weight: 700; }
    .td__emplyoeeId { font-size: 14px; padding-bottom: 25px; color: blue; font-weight: 900; text-align: center }
    .td__sponser { color: green; font-weight: 300; text-align: center }
    .sponser__name { font-size: 10px; font-weight: 100; }
    .country { color: red; text-align: center; font-size: 10px; }
    .employe__trade { color: red; text-align: center; font-size: 10px; }
    .td__project { font-size: 8px; padding-bottom: 5px; color: red; font-weight: 100; text-align: center; }
    .td__multi__project { font-size: 10px; padding-right: 20px; color: red; text-align: right; }
    .td__red__color { color: red; text-align: center; font-size: 10px; }
    .td__total { font-weight: bold; text-align: center; color: black; font-size: 14px; }
    .box__signature { width: 150px; height: 30px; border: 1px solid blue; margin: 5px auto 0; color: gray; }
    .td__gross_salary { font-size: 14px; color: navy; font-weight: 900; text-align: center }
</style>
</head>

<body>
    <div class="watermark-container">
        <!-- Angled/Rotated Watermark -->
        <div class="watermark">
            {{ strtoupper($company->comp_name_en ?? 'CONFIDENTIAL') }}
        </div>

        <div class="main__wrap">
            <!-- header part-->
            <section class="header__part">
                <div class="date__part">
                    <p><strong> Salary Month : {{ $monthName }}, {{$salaryYear}} </strong></p>
                </div>
                <div class="title__part">
                    <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small></h4>
                    <address class="address">
                        {{$company->comp_address}}
                    </address>
                </div>
                <div class="print__part">
                    <p><strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }}</p>
                    <button type="button" onclick="window.print()" class="print__button">Print</button>
                </div>
            </section>

            <!-- table part -->
            <section class="table__part">
                <table id="employeeinfo">
                    <thead>
                        <tr>
                            <th colspan="13" style="text-align: center;">{{ $monthName }}, {{$salaryYear}}</th>
                        </tr>
                        <tr>
                            <th>SL<br>No</th>
                            <th>Emp.<br>ID</th>
                            <th>Employee Name</th>
                            <th>Iqama No<br><br>BS/Rate</th>
                            <th>Sponsor<br><br>Basic Hrs.</th>
                            <th>Nat<br><br>OT/Amt</th>
                            <th>Working<br>Days<hr>P &nbsp; &nbsp; L</th>
                            <th>Trade<br><br>(Food+Other)</th>
                            <th><br>Total<br>Salary</th>
                            <th style="text-align: right;">Deduction<br><hr>(S.T+Adv.+C.D)</th>
                            <th style="width:50px;white-space: normal;"><br><hr>ID+P.Amnt</th>
                            <th><br>Net<br>Salary</th>
                            <th>Project<br>Signature</th>
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
                        $totalPartialAmnt = 0;
                        @endphp

                        @foreach($salaryReport as $salary)
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
                        $totalFoodDeduction += $salary->slh_food_deduction;
                        $totalPartialAmnt += $salary->partial_paid_amount;
                        $total_slh_all_include_amount += $salary->slh_all_include_amount;
                        $total_others_amount = $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount;
                        $ground_total_others_amount += $total_others_amount;
                        $multi_pro_total_hour = "";
                        $multi_project = "";
                        $findMultipleProjectEmployee = App\Models\EmployeeMultiProjectWorkHistory::where('emp_id',$salary->emp_auto_id)->where('month',$salary->slh_month)->where('year',$salary->slh_year)->get();
                        @endphp

                        <tr style="border-bottom:0;">
                            <td class="td__center td__name">{{ $loop->iteration }}</td>
                            <td class="td__center td__emplyoeeId">{{ $salary->employee_id }}</td>
                            <td class="td__left td__name"><span>{{ $salary->employee_name }}</span></td>
                            <td class="td__center">{{ $salary->akama_no }}<br><br>{{ $salary->basic_amount }}/{{ $salary->hourly_rent }}</td>
                            <td class="td__sponser"><span class="sponser__name">{{Str::limit($salary->spons_name,12)}}</span><br>{{ $salary->slh_total_hours }}</td>
                            <td class="td__center"><span class="country">{{ Str::limit($salary->country_name,3) }}</span><br><br>{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>
                            <td class="td__sponser">{{ $salary->slh_total_working_days - $salary->paid_leave }}&nbsp;&nbsp;{{ $salary->paid_leave }}<br><br>{{ $salary->slh_total_working_days }}</td>
                            <td class="td__center"><span class="employe__trade">{{ Str::limit($salary->catg_name,10) }}</span><br><br>{{round($salary->food_allowance) }} + {{ round($total_others_amount) }}</td>
                            <td class="td__gross_salary"><br>{{round($salary->slh_all_include_amount)}}</td>
                            <td class="td__center" style="text-align: right;"><br>{{ round($salary->slh_saudi_tax) }} + {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}</td>
                            <th><br>{{round($salary->slh_iqama_advance)}} <br>{{ round($salary->partial_paid_amount) }}</th>
                            <td class="td__gross_salary"><br>{{ round($salary->slh_total_salary) }}</td>
                            <td class="td__project">{{ Str::limit($salary->project->proj_name,40) }}<br>
                                <div class="box__signature">
                                    @if($salary->Status == 0)
                                        @if($salary->salary_status >= 2)
                                            <b> Salary Hold </b><br>{{ $salary->title }}
                                        @endif
                                        <br>Unpaid
                                    @else
                                        <br>{{ $salary->slh_paid_method == true ? 'Paid by Bank':'Paid by Cash'}}
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
                            <td class="td__center"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="3" class="td__multi__project">{!! $multi_project !!}</td>
                        </tr>
                        @endif
                        @endforeach

                        <tr>
                            <td class="td__total" colspan="3" >Total  </td>
                            <td class="td__total">Total Hours <br> {{ $totalHours+$totalOverTimeHours }}   </td>
                            <td class="td__total">Basic Hrs<br>{{ $totalHours }}</td>
                            <td class="td__total">O.T Hrs<br>{{$totalOverTimeHours}}  </td>
                            <td class="td__total">O.T Amount<br> {{ round($totalOverTimeAmount) }}</td>
                            <td class="td__total">Food+Other<br>{{round($totalFoodAllowance)}}<br>{{ round($ground_total_others_amount) }}</td>
                            <td class="td__total">Total<br>Salary<br>{{ round($totalSalaryAmount + $totalSaudiTax + $totalIqamaRenewal +$totalOtherAdvance +$totalContribution + $totalFoodDeduction) }}</td>
                            <td class="td__total">S.T+Adv+C.D<br>{{ round($totalSaudiTax) }}+{{ round($totalOtherAdvance) }}<br>+{{ round($totalFoodDeduction) }}</td>
                            <td class="td__total">ID+P.Amnt<br>{{ round($totalIqamaRenewal) }} <br>{{  round($totalPartialAmnt) }}  </td>
                            <td class="td__total">Net<br>Salary<br>{{ round($totalSalaryAmount) }}</td>
                            <td class="td__project">{{""}}<br>{{""}}</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section>
                <div class="officer-signature" style="padding-top: 60px; display: flex; justify-content:space-between; text-align: center;">
                    <p><b>------------------------<br>{{ $login_name }}</b><br>Prepared By</p>
                    <p><b>------------------------<br></b>Accounts/HR</p>
                    <p><b>------------------------<br></b>Approved By</p>
                </div>
            </section>
        </div>



    </div>
</body>

</html>
