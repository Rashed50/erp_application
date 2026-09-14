<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All EmpLeave Salary Generate</title>

    <style type="text/css">
    /* bootstrap code start */


        /* bootstrap code end */
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }

        .salary {
            margin-top: 50px;
        }


        .table> :not(caption)>*>* {
            padding: 0.2rem .5rem;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border: 1px;
        }

        /* heading */
        .company_information {}

        .company_information h4 {
            font-size: 18px;
            font-weight: 700;
            line-height: 2px;
        }

        .company_information h4 small {}

        .company_information .address {
            margin: 0;
            color: green;
            font-size: 16px;
        }

        .company_information p {}

        .company_information p span {
            font-size: 13px;
            font-weight: 500;
        }

        .salary__header {
            display: flex;
            margin-bottom: 25px;
        }

        .salary__header .project_info {
            margin-left: 15px;
            margin-top: 4px;
        }

        .salary__header .project_info span {
            display: block;
        }

        .salary__header .project_info span.project_name {
            font-size: 18px;
            font-weight: 700;
        }

        .salary__header .project_info span.project_code {
            font-size: 14px;
            font-weight: 600;
        }


        /* heading */



        .salary__table {}

        .salary__table thead {
            border: 1px solid #3F3F3F;
        }

        .salary__table td.signature_field {
            border: 1px solid #3F3F3F;
            border-top: 0;
            border-left: 0;
        }

        .salary__table thead tr.thead-row th {}

        tr.salary-row-parent {
            border: 1px solid #3F3F3F;
            background-color: #FAFAFA;
        }



        .salary__table tbody tr.first-row {
            border: 0px solid #FAFAFA;
            border-left: 1px solid #3F3F3F;
            border-bottom: 0;
            background-color: #FAFAFA;
        }

        .salary__table tbody tr.second-row {
            border-top: 0 !important;
            border: 1px solid #3F3F3F;
        }

        .salary__table thead tr.first-head-row th,
        .salary__table thead tr.second-head-row th {
            font-size: 11px;
            font-weight: 700;
        }

        .salary__table thead tr.first-head-row th {}

        .salary__table thead tr.first-head-row th span.basic_amount {
            text-align: right;
            display: block;
        }

        .salary__table thead tr.second-head-row th {}

        .salary__table thead tr.second-head-row th .nationality {}

        .salary__table thead tr.second-head-row th .district {}

        .salary__table thead tr.second-head-row th {
            padding-bottom: 0;
        }

        /* Salary Heading */
        th span.district-head,
        th span.category-head,
        th span.iqama-head,
        th span.type-head,
        th span.country-head {
            border: 1px solid #ddd;
            border-bottom-color: rgb(221, 221, 221);
            border-bottom-style: solid;
            border-bottom-width: 1px;
            display: block;
            border-bottom: 0;
            text-align: center;
            color: red;
        }

        th span.type-head,
        th span.iqama-head,
        th span.district-head {
            color: teal;
        }

        th span {
            display: block;
            color: #222;
            font-size: 11px;
            font-weight: 700;
        }

        td span {
            display: block;
            color: #222;
            font-size: 10px;
            font-weight: 700;
        }


        td span.district-data,
        /* td span.iqama-data, */
        td span.type-data,
        td span.category-data,
        td span.country-data {
            text-align: center;
        }





        td span.basic_amout {
            display: block;
            text-align: right;
        }

        td span.emp_name {
            text-transform: capitalize;
        }

        td span.signature {
            display: block;
            background-color: #fff;
            height: 47px;
        }

        td span.iqama_no {
            text-align: right;
            display: block;
            padding-right: 40px;
            color: #222;
            font-weight: 700;
            font-size: 11px;
        }

        td span.country {
            color: red;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        td span.district {
            color: teal;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .salary__header-bottom p.date {
            background: teal;
            display: inline-block;
            padding: 3px 10px;
            color: #fff;
        }

        /* ============== Salary Table */
        span.currency {
            color: green;
        }

        span.currency_data {
            color: green;
        }

        .salary__header-bottom {
            margin-bottom: 20px;
        }

        a.print-button {
            text-decoration: none;
            background: teal;
            color: #fff;
            padding: 5px 10px;
            cursor: pointer;
        }

        p.toEndDate {}

        p.toEndDate strong {
            font-size: 14px
        }

        p.toEndDate span {
            font-size: 14px;
            font-weight: 600;
            margin-left: 2px;
        }

        div.officer-signature {
            display: flex;
            justify-content: space-between;
        }

        span {
            font-size: 10px !important;
        }

        .project_name {
            margin-bottom: 0;
            font-size: 11px;
            font-weight: bold;
        }

        /* ========== Print Style ========== */


       

        table.attend__table thead tr th {
            font-size: 13px;
            font-weight: 700;
        }

        table.attend__table tr {
            border: 1px solid #333
        }

        table.attend__table th {
            border: 1px solid #333
        }

        table.attend__table td {
            border: 1px solid #333
        }

        span.attendence-data {
            font-size: 14px;
            display: block;
        }


        table,
        th,
        td {
            border-collapse: collapse;
            border: 1px solid black;
        }

        th,
        td {

            border-radius: 10px;
            text-align: center;
            border: 1px solid black;
        }

        /* tr:nth-child(even) {
		  background-color: rgba(150, 212, 212, 0.4);
		}

		th:nth-child(even),td:nth-child(even) {
		  background-color: rgba(150, 212, 212, 0.4);
		} */

		@media print {
            .container {
                max-width: 98%;
                margin: 0 auto 50px;

            }

			
			.breack {
                page-break-after: always;
            }

            button,
            a {
                display: none;
            }

            @page {
                size: A4 landscape;
                margin: 25mm 10mm 20mm 10mm;
                /* top, right,bottom, left */

            }
            /*__________ Salary Attendence __________*/
            table.attend__table tr {
                border: 1px solid #333 !important
            }

            table.attend__table th {
                border: 1px solid #333
            }

            table.attend__table td {
                border: 1px solid #333
            }

            /*__________ Salary Attendence __________*/

            a.print-button {
                display: none;
            }

            p.toEndDate {}

            p.toEndDate strong {
                font-size: 14px
            }

            p.toEndDate span {
                font-size: 14px;
                font-weight: 600;
                margin-left: 2px;
            }

            div.officer-signature {
                display: flex;
                justify-content: space-between;
            }

            .project_name {
                font-size: 14px;
            }

            span {
                font-size: 10px !important;
            }

            .totalCount {
                font-size: 14px;
            }

            .officer-signature {
                margin-top: 100px;
            }

        }
    </style>
</head>
		@php 
		
		use App\Http\Controllers\Admin\CompanyProfileController;
		use App\Http\Controllers\Admin\Helper\HelperController;

		$month = Carbon\Carbon::now()->format('m');
   		$year = Carbon\Carbon::now('Y');

		$HelperOBJ = new HelperController();
		$monthName = $HelperOBJ->getMonthName($month);

		/* ========== Company Profile ========== */
		$companyOBJ = new CompanyProfileController();
		$company = $companyOBJ->findCompanry();
		
		$salaryReport = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->get();
		$allSalaryAmount = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_total_salary');
		$iqamaAmount = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_iqama_advance');
		$totalHours = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_total_hours');
		$totalSaudiTax = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_saudi_tax');
		$totalOverTimeHours = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_total_overtime');
		$totalOverTimeAmount = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('slh_overtime_amount');
		$totalFoodAllowance = App\Models\SalaryHistory::where('slh_month',$month)->where('slh_year',$year)->sum('food_allowance');

		

			if($salaryReport){
				// Condition Wise Check
					foreach ($salaryReport as $salary){
						if($salary->hourly_employee == true){
						$others = $salary->others;
						}else{
						$others = ($salary->house_rent + $salary->conveyance_allowance + $salary->medical_allowance + $salary->others);
						}
						// Condition Wise Check
						$salary->otherAmount = $others;
					}
				}

		@endphp
<body>
    <section class="salary">
        <div class="container" style="width:100%">
            <!-- salary header -->
            <div class="row align-center">
                <div class="col-md-6">
                    <div class="salary__header">

                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>
            <!-- salary bottom header -->
            <div class="salary__header-bottom">
                <div class="row">
                    <div class="col-md-3">
                        <div class="project_info" style="margin-left:0">
                            <p class="project_name"> <strong>Salary Month :</strong> {{ $monthName }},2021 </p>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="company_information" style="text-align:center">
                            <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                            <address class="address">
                                {{$company->comp_address}}
                            </address>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="salary__download"
                            style="text-align:right; display:flex-; justify-content: right; align-items:center; margin-bottom: 10px">
                            <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                            <a href="{{ route('download.pdf-salary') }}" class="print-button">PDF</a>
                            <a onclick="window.print()" class="print-button"> Pirnt</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- salary table -->


            <div class="salary__table-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-responsive salary__table" style="width:100%">
                            <thead>
                                @php $counter = 0; @endphp
                                <tr>
                                <tr class="first-head-row">
                                    <th> <span>S.N</span> </th>
                                    <th colspan="4"> <span>Employee Name</span> </th>
                                    <th> <span>Iqama No</span> </th>
                                    <th> <span>Nation</span> </th>
                                    <th> <span>Trade</span> </th>
                                    <th> <span></span> </th>
                                    <th> <span>Sponser</span> </th>
                                    <th> <span></span> </th>
                                    <th> <span></span> </th>
                                    <th></th>
                                    <th> <span>Signature</span> </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr class="second-head-row">
                                    <th></th>
                                    <th> ID</th>
                                    <th> <span class="currency"> </span> </th>
                                    </th>
                                    <th> <span class="currency"> BS/Rate </span> </th>
                                    <th> <span class="currency"> OT/Amt </span> </th>
                                    <th> <span class="currency">Hr/WrD</span> </th>
                                    <th> <span class="currency"> Total(Food+Other) </span> </th>
                                    <th> <span class="currency"> (Adv1+Adv2) </span> </th>
                                    <th> <span class="currency"> BD Amt </span> </th>
                                    <th> <span class="currency"> Contribution </span> </th>
                                    <th> <span class="currency"> ID Renew </span> </th>
                                    <th> <span class="currency"> Gross Salary </span> </th>
                                    <th></th>
                                    <th>Project</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($salaryReport as $key=>$salary)

                                @php
                                $salary->otherAmount = 200;
                                $salary->slh_other_advance = 300;
                                $salary->slh_total_overtime = 3000;
                                $salary->slh_overtime_amount =3000.30;
                                $salary->slh_total_hours = 900;
                                $salary->food_allowance = 300;
                                $salary->slh_total_working_days = 29;
                                $salary->slh_saudi_tax = 300;
                                $salary->slh_cpf_contribution = 9000;
                                $salary->employee->employee_name = 'Md Rashedul Hoque Rashedul 78931234567890';
                                $salary->employee->category->catg_name = '12345678901234567890';
                                $salary->employee->sponser->spons_name = '12345678901234567890';
                                @endphp


                                <tr class="salary-row-parent">
                                    <!-- first tr -->
                                <tr class="first-row">
                                    <td> <span>{{ $loop->iteration }}</span> </td>
                                    <td colspan="4"> <span> {{Str::limit( $salary->employee->employee_name,30) }}
                                        </span> </td>
                                    <td colspan="1"> <span>{{ $salary->employee->akama_no }}</span> </td>
                                    <td colspan="1">
                                        <span>{{ Str::limit($salary->employee->country->country_name,6) }}</span>
                                    </td>
                                    <td colspan="2">
                                        <span>{{ Str::limit($salary->employee->category->catg_name,17) }}</span>
                                    </td>

                                    <td colspan="2">
                                        <span>{{Str::limit( $salary->employee->sponser->spons_name,17)}}</span>
                                    </td>

                                    <td></td>
                                    <td colspan="4" rowspan="2" class="signature_field"> <span class="signature">
                                            <span
                                                style="padding: 3px 5px">{{ Str::limit($salary->employee->project->proj_name,40) }}</span>
                                            <br>
                                        </span> </td>
                                </tr>
                                <!-- second tr -->
                                <tr class="second-row">
                                    <td></td>
                                    <td> <span class="currency_data">{{ $salary->employee->employee_id }}</span> </td>
                                    <td> <span class="currency_data"></span> </td>
                                    <td> <span class="currency_data">{{ $salary->basic_amount }}/{{ $salary->hourly_rent }}</span></td> 
                                    <td> <span class="currency_data">{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</span> </td>
                                    <td> <span
                                            class="currency_data">{{ $salary->slh_total_hours }}/{{ $salary->slh_total_working_days }}</span>
                                    </td>
                                    <td> <span
                                            class="currency_data">{{ $salary->food_allowance + $salary->otherAmount }}({{round($salary->food_allowance) }}
                                            + {{ $salary->otherAmount }})</span> </td>
                                    <td> <span class="currency_data">({{ round($salary->slh_saudi_tax) }} +
                                            {{round($salary->slh_other_advance) }}) </span> </td>

                                    <td> <span class="currency_data">0</span> </td> <!-- iqama -->
                                    <td> <span class="currency_data">{{ round($salary->slh_cpf_contribution)}}</span>
                                    </td> <!-- iqama -->
                                    <td> <span class="currency_data"> {{round( $salary->slh_iqama_advance) }} </span>
                                    </td>
                                    <td> <span class="currency_data"> {{ $salary->slh_total_salary }} </span> </td>
                                </tr>
                                </tr>




                                @if( $key >1 && $key%5==0)
                               		 <div class="breack" style="page-break-after: always;"></div>
								@endif
                          
                                @endforeach

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="3">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Total Work
                                                Hours:</strong> {{ $totalHours }} </p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Iqama Renew:</strong>
                                            {{ $iqamaAmount }} </p>
                                    </td>

                                    <td colspan="2">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Total S.T:</strong>
                                            {{ $totalSaudiTax }} </p>
                                    </td>

                                    <td colspan="2">
                                        <p style="text-align:right;margin-bottom:0" class="totalCount"> <strong>Total
                                                Salary Amount:</strong> </p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0" class="totalCount"> {{ $allSalaryAmount }} </p>
                                    </td>


                                </tr>

                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="3">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Total Over Time
                                                Hours:</strong> {{ $totalOverTimeHours }} </p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Total Over Time
                                                Amount:</strong> {{ $totalOverTimeAmount }} </p>
                                    </td>

                                    <td colspan="2">
                                        <p style="margin-bottom:0" class="totalCount"> <strong>Total Food
                                                Allowance:</strong> {{$totalFoodAllowance}} </p>
                                    </td>

                                    <td colspan="2">
                                        <p style="text-align:right;margin-bottom:0" class="totalCount">
                                            <strong></strong>
                                        </p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom:0" class="totalCount"> </p>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                        {{-- Officer Signature --}}
                        <div class="row">
                            <div class="officer-signature">
                                <p>Operation Manager</p>
                                <p>Accountant</p>
                                <p>General Manager</p>
                            </div>
                        </div>
                        {{-- Officer Signature --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>