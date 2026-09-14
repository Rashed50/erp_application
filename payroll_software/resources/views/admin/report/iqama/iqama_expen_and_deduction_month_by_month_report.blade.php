<!DOCTYPE html>
<html lang="en">

<head>
    <title>Iqama Exp-Deduct Summary</title>

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
                size: A4 portrait;
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
            font-size: 18px;
        }

        .title__part {
            text-align: center;
            font-size: 18px;
        }

        .title_left__part {
            text-align: left;
            font-size: 11px;
        }

        .address {
            font-size: 13px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }


        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo td,
        #employeeinfo th {
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
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;

        }
        .amount__td {

            text-align: right;
            font-weight:normal;
            margin-right:5px;

        }
        .total__td {

            text-align: right;
            font-weight:bold;
            margin-right:5px;

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
            padding-left: 5px;
            text-transform: capitalize
        }

        .td__iqama {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center;
            text-transform: capitalize
        }


        .td__amount {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: right;
            padding-right: 10px;
        }

        .td__total_amount {
            font-size: 12px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
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

        <h3 style="text-align:center"> Iqama Expense and Collection Summary</h3><br>
        <!-- table part -->
        {{-- <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th> <span>S.N</span> </th>
                        <th> <span>Month/Year</span> </th>
                        <th> <span>Expense</span> </th>
                        <th> <span>Collection</span> </th>
                        <th> <span>Balance</span> </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summary as $index => $data)
                    <tr>
                        <td class="td__s_n"> <span>{{ $index + 1 }}</span> </td>
                        <td class="td__amount"> <span>{{ $data['month_name'] }} / {{ $data['year'] }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['expense'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['collection'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['balance'], 2) }}</span> </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="td__total_amount" colspan="2"> <strong>Total</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['expense'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['collection'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['balance'], 2) }}</strong> </td>
                    </tr>
                </tbody>
            </table>
        </section> --}}



        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th> <span>S.N</span> </th>
                        <th> <span>Month/Year</span> </th>
                        <th> <span>Employee Paid Expense</span> </th>
                        <th> <span>Company Paid Expense</span> </th>
                        <th> <span>Total Expense</span> </th>
                        <th> <span>Collection</span> </th>
                        <th> <span>Balance</span> </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summary as $index => $data)
                    <tr>
                        <td class="td__s_n"> <span>{{ $index + 1 }}</span> </td>
                        <td class="td__amount"> <span>{{ $data['month_name'] }} / {{ $data['year'] }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['employee_expense'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['company_expense'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['total_expense'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['collection'], 2) }}</span> </td>
                        <td class="td__amount"> <span>{{ number_format($data['balance'], 2) }}</span> </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="td__total_amount" colspan="2"> <strong>Total</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['employee_expense'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['company_expense'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['total_expense'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['collection'], 2) }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{ number_format($total['balance'], 2) }}</strong> </td>
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
                    <p>Prepared By<br /><br /><br></p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
