<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pre-vacation Report</title>
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
                margin: 0 auto 5px;


            }

            @page {
                size: A4 landscape;
                margin: 10mm 0mm 5mm 10mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

        }


        .main__wrap {
            width: 95%;
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
            font-size:14px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
          border: 0.5px solid gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 12px;
            border: 0.5px solid gray;
            font-weight: bold;
            padding: 3px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 12px;
            border: 1px ridge gray;
            padding: 3px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }

        .th_header {
            font-size: 12px;
            color: black;
            background:#B3B6B7;
            font-weight: bold;
            text-align: center;
            text-transform: capitalize;
            /* height:30px; */
        }

        .td__s_n {
            font-size: 12px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 12px;
            color: black;
            text-align: center;
            font-weight: 300;
        }
        .td__emplyoee_info {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize

        }

        .td__iqama {
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__country{
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__emp_trade {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__amount {

            font-size: 12px;
            color: black;
            text-align: right;
        }
         .td__total_amount {

            font-size: 12px;
            color: black;
            text-align: right;
            font-weight: bold;
        }


    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong>  {{$report_title}}</strong></p>
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
                <p> <strong>Print:</strong> {{ Carbon\Carbon::now()  }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th class="th_header" rowspan="2"> <span>S.N</span> </th>
                  <th class="th_header" rowspan="2"> <span>ID</span> </th>
                  <th class="th_header" rowspan="2"> <span>Employee Name</span> </th>
                  <th class="th_header" rowspan="2"> <span>Iqama</span> </th>
                  <th class="th_header" rowspan="2"> <span>Trade</span> </th>
                  <th class="th_header" rowspan="2"> <span>Sponsor</span> </th>
                  <th class="th_header" rowspan="2"> <span>Project Name</span> </th>
                  <th class="th_header" rowspan="2"> <span>Salary<br>Type</span> </th>
                  <th class="th_header" rowspan="2"> <span>Closing<br> Balance</span> </th>
                  <th class="th_header" rowspan="2"> <span>Unpaid <br>Salary</span> </th>
                  <th class="th_header" colspan="3" >Iqama Details </th>
                  <th class="th_header" colspan="3">Advance Details</th>
                  <th class="th_header" rowspan="2">Payable <br> Balance </th>
                  <th class="th_header" rowspan="2">Receivable <br>Balance </th>
                </tr>
                <tr>

                    <td class="th_header">Renewal Cost</td>
                    <td class="th_header">Deducted</td>
                    <td class="th_header">Remaining</td>

                    <td class="th_header">Received</td>
                    <td class="th_header">Deducted</td>
                    <td class="th_header">Remaining</td>
                </tr>
                </thead>
                <tbody>
                    @php
                        $total_payable = 0;
                        $total_receivable =0;
                    @endphp
                @foreach ($report_records as $emp)

                    @php
                        $emp->iqama_balance = $emp->closed_fiscal_record->balance_amount + $emp->iqama_renewal_total_expence  - (  $emp->toal_iqama_expense_deduction_from_salary +  $emp->cash_receive_total_paid_amount  );
                        $emp->advance_balance =   $emp->other_advance_total_Amount  - $emp->total_other_advace_deduction_from_salary;
                        $emp->final_balance = round( $emp->iqama_balance  + $emp->advance_balance) ;
                        $emp->total_unpaid_salary_amount = round($emp->total_unpaid_salary_amount);
                        //  $emp->final_balance <= 0 ?  'Payable' :'Receivable'

                        if ($emp->final_balance <= 0)
                          {
                            $total_payable +=  ($emp->final_balance *(-1) )+ $emp->total_unpaid_salary_amount;
                            $emp->is_payable = true;
                            $emp->final_balance = ($emp->final_balance *(-1) )+ $emp->total_unpaid_salary_amount;
                          }
                        else {
                             if($emp->final_balance - $emp->total_unpaid_salary_amount < 0 ){
                                $total_payable +=  ($emp->final_balance - $emp->total_unpaid_salary_amount)*-1 ;
                                $emp->is_payable = true;
                                $emp->final_balance = ($emp->final_balance - $emp->total_unpaid_salary_amount)*-1 ;
                             }
                             else{
                                $total_receivable += $emp->final_balance - $emp->total_unpaid_salary_amount;
                                $emp->is_payable = false;
                                $emp->final_balance = $emp->final_balance - $emp->total_unpaid_salary_amount;
                            }
                        }

                    @endphp

                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id">{{$emp->employee_id}}</span> </td>
                  <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }}  </td>
                  <td class="td__emp_trade"> {{ $emp->catg_name }} </td>
                  <td class="td__emp_trade"> {{  Str::limit($emp->spons_name,12)   }}</td>
                  <td class="td__country"> {{ Str::limit($emp->proj_name,12) }}</td>
                  <td class="td__country"> {{  $emp->hourly_employee == 1 ? "Hourly": "Basic" }} <br> {{ $emp->hourly_employee == 1 ? $emp->hourly_rent: $emp->basic_amount }} </td>
                  <td class="td__amount"> {{  $emp->closed_fiscal_record->balance_amount }} </td>
                  <td class="td__amount"> {{   $emp->total_unpaid_salary_amount }} </td>
                  <td class="td__amount"> {{ $emp->iqama_renewal_total_expence + $emp->closed_fiscal_record->balance_amount }} </td>
                  <td class="td__amount"> {{ $emp->toal_iqama_expense_deduction_from_salary + $emp->cash_receive_total_paid_amount }} </td>
                  <td class="td__amount"> {{   $emp->iqama_balance }} </td>
                  <td class="td__amount"> {{   $emp->other_advance_total_Amount }} </td>
                  <td class="td__amount"> {{   $emp->total_other_advace_deduction_from_salary }} </td>
                  <td class="td__amount"> {{   $emp->advance_balance }} </td>
                  @if($emp->is_payable)
                    <td class="td__total_amount"> {{  $emp->final_balance }}</td>
                    <td class="td__amount">-</td>
                  @else
                    <td class="td__amount"> -</td>
                    <td class="td__total_amount"> {{ $emp->final_balance }}</td>
                  @endif
                  {{-- <td class="td__amount">
                        @if ($emp->final_balance <= 0)
                            Payable <br>
                            {{  ($emp->final_balance *(-1) )+ $emp->total_unpaid_salary_amount }}
                        @else
                            @if($emp->final_balance - $emp->total_unpaid_salary_amount < 0 )
                                Payable <br> {{  ($emp->final_balance - $emp->total_unpaid_salary_amount)*-1 }}
                            @else
                                receivable <br> {{ $emp->final_balance - $emp->total_unpaid_salary_amount  }}
                            @endif
                        @endif
                    </td> --}}

                 </tr>
                @endforeach
              </tbody>
              <tr>
                <td class="td__total_amount" colspan="16">Total = </td>
                <td class="td__total_amount"> {{ $total_payable}}</td>
                <td class="td__total_amount"> {{$total_receivable }}</td>
              </tr>

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

</html>

