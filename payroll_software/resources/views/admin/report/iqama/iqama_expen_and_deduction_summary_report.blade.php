
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 90%;
                margin: 0 auto 10px;
            }

            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            a:link {
             text-decoration: none;
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

        .title__part h6 {
            color: rgb(38, 104, 8);
            text-align: center;
            font-size:18px;
        }

        .title__part{
            text-align: center;
            font-size:18px;
        }
        .title_left__part{
            text-align: left;
            font-size:11px;
        }
        .address {
            font-size:13px;
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
          border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 10px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_name {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            padding-left:5px;
            text-transform:capitalize
        }
        .td__iqama {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center; 
            text-transform:capitalize
        }

        .td__amount {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: right;
            padding-right:10px;
        }
        .td__total_amount {
            font-size: 10px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right:10px;
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
                <p> <strong> Project : {{$projectName}} </strong></p>
                <p> <strong> Reporting Month : {{$reportingMonth->month_name}} {{$year}} </strong></p>
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

        <h3 style="text-align:center"> Employee Iqama Renewal Deduction Summary</h3><br>
         <!-- table part -->
         <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th> <span>S.N</span> </th>
                        <th> <span>Id</span> </th>
                        <th> <span>Name</span> </th>
                        <th> <span>Iqama No.</span> </th>
                        <th> <span>Sponsor</span> </th>
                        <th> <span>Total Expense</span> </th>
                        <th> <span>Total Paid </span> </th>
                        <th> <span>Deduction <br> {{$reportingMonth->month_name}}</span> </th>
                        <th> <span>Due Amount</span> </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projAndSponsWiseEmp as $emp)
                    <tr>
                        <td class="td__s_n"> <span>{{ $loop->iteration }}</span> </td>
                        <td class="td__employee_id"> <span>{{ $emp->employee_id }}</span> </td>
                        <td class="td__emplyoee_name"> <span>{{ $emp->employee_name }}</span> </td>
                        <td class="td__iqama"> <span>{{ $emp->akama_no }}</span> </td>
                        <td class="td__iqama"> <span>{{ $emp->sponsor->spons_name }}</span> </td> 
                        <td class="td__amount"> <span>{{ $emp->iqama_renewal_cost_total_amount }}</span> </td>
                        <td class="td__amount"> <span>{{ $emp->iqama_renewal_total_paid_amount }}</span> </td>
                        <td class="td__amount"> <span>{{ $emp->iqamaDeductionAmount }}</span> </td>
                        <td class="td__amount"> <span>{{ $emp->iqama_renewal_cost_total_amount - $emp->iqama_renewal_total_paid_amount }}</span></td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="td__total_amount" colspan="5"> <strong> Total </strong> </td>
                        <td class="td__total_amount"> <strong>{{ $totalIqmaAdvAmount }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ $totalPaidAmount }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ $totalDueAmount }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->



    <section>
        <br><br>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br></p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>









